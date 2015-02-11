<?php

namespace ObjectRelationMapper\Search;

use ObjectRelationMapper\Base\AORM;

abstract class ASearch
{

	/**
	 * @var ORM
	 */
	protected $orm;
	protected $search = Array();
	protected $params = Array();
	protected $joinTables = Array();
	protected $selectCols = Array();
	protected $aliases = Array();
	protected $searchCount = 0;
	protected $query;
	protected $countQuery;
	protected $limit = 999999;
	protected $offset = 0;
	protected $imploder = ' AND ';
	protected $ordering = Array();
	protected $results = Array();
	protected $group = Array();
	protected $functionColumn = Array();
	protected $additionalOrms = Array();

    const RESULTS_BY_FETCH_NUM = false;
    const RESULTS_BY_FETCH_ASSOC = true;

	/**
	 * Standardni construct
	 * @param AORM $orm
	 */
	public function __construct(AORM $orm)
	{
		$this->orm = $orm;
		$this->aliases = $orm->getAllAliases();
		$this->selectCols[$orm->getConfigDbTable()] = $orm->getAllDbFields(NULL, true);
	}

	/**
	 * Prida Parametr
	 * @param $value
	 * @return string
	 */
	protected function addParameter($value)
	{
		$this->searchCount++;
		$this->params[] = Array(':param' . $this->searchCount, $value);
		return ':param' . $this->searchCount;
	}

	/**
	 * Prida childa, kdyz nechceme vyhledavat podle parametru
	 * @param $childName
	 * @param string $joinType
	 * @param array $additionalCols
	 * @param string $matching
	 * @return ORM
	 */
	protected function addChild($childName, $joinType = 'LEFT', $additionalCols = Array(), $matching = '=')
	{
		if (!isset($this->additionalOrms[$childName])) {
			$child = $this->orm->{'getChild' . ucfirst($childName) . 'Config'}();
			$orm = new $child->ormName();
			$this->additionalOrms[$childName] = $orm;

			$join = ' ' . $joinType . ' JOIN ' . $orm->getConfigDbTable() . ' ON
					' . $this->orm->getConfigDbTable() . '.' . $child->localKey . ' = ' . $orm->getConfigDbTable() . '.' . $child->foreignKey . ' ';

			foreach ($additionalCols as $col => $value) {
				$join .= ' AND ' . $orm->getDbField($col, true) . ' ' . $matching . ' ' . $this->addParameter($value) . ' ';
			}

			$this->joinTables[$childName] = $join;
			$this->selectCols[$childName] = $orm->getAllDbFields(NULL, true);
		}

		return new $this->additionalOrms[$childName];
	}

	/**
	 * Prida DbField
	 * @param $field
	 * @return string
	 */
	protected function dbFieldName($field)
	{
		if (preg_match('/(.*)\.(.*)/', $field, $matches)) {
			return $this->getOrmDbColumn($this->addChild($matches[1]), $matches[2]);
		} else {
			$this->aliasExists($field);
			return $this->getOrmDbColumn($this->orm, $field);
		}
	}

	/**
	 * Vrati column z childa
	 * @param AORM $orm
	 * @param $alias
	 * @return string
	 */
	protected function getOrmDbColumn(AORM $orm, $alias)
	{
		return $orm->getDbField($alias, true);
	}

	/**
	 * Vrati, zda na ORM existuje Alias
	 * @param $property
	 * @throws \ObjectRelationMapper\Exception\ORM
	 */
	protected function aliasExists($property)
	{
		if (!in_array($property, $this->aliases)) {
			throw new \ObjectRelationMapper\Exception\ORM('Alias ' . $property . ' neexistuje na ORM ' . $this->orm->getConfigObject());
		}
	}

	/**
	 * Vrati query
	 * @return mixed
	 */
	public function getQuery()
	{
		return $this->composeLoadQuery();
	}

	/**
	 * Vrati count query
	 * @return mixed
	 */
	public function getCountQuery()
	{
		return $this->composeCountQuery();
	}

	/**
	 * Vrati count
	 * @return int
	 */
	public function getCount()
	{
		return $this->orm->countByQuery($this->composeCountQuery(), $this->params);
	}

	/**
	 * Vrati text count query
	 * @return string
	 */
	protected function composeCountQuery()
	{
		$query = 'SELECT count(' . $this->orm->getConfigDbTable() . '.' . $this->orm->getConfigDbPrimaryKey() . ') AS count FROM ' . $this->orm->getConfigDbTable() . ' ';

		if (!empty($this->joinTables)) {
			$query .= ' ' . implode(' ', $this->joinTables);
		}

		if (!empty($this->search)) {
			$query .= ' WHERE ' . implode($this->imploder, $this->search);
		}

		return $query;
	}

	/**
	 * Vrati vsechny vysledky
	 * @param int $fetchType
     * @return Array
     */
    public function getResults($fetchType = \Query::FETCH_ASSOC)
	{
        if (empty($this->results)) {
			$queryBuilder = $this->orm->getQueryBuilder();
            $this->results = $queryBuilder->loadByQuery($this->orm, $this->composeLoadQuery(), $this->params, $fetchType);
            if($fetchType == \Query::FETCH_NUM){
                $this->results = $this->renameFieldsFromFetchNum($this->results);
            }
		}

		return $this->orm->loadMultiple($this->results, $fetchType);
	}

	/**
	 * Vrati vysledky s childama podle master ORM
	 * @return Array
	 */
	public function getResultsWithChildsLoaded()
	{
		$return = Array();

		foreach ($this->getResults(\Query::FETCH_NUM) as $orm) {
			$return[$orm->primaryKey] = $orm;
		}

		foreach ($this->additionalOrms as $child => $load) {
			$childs = Array();
			$childConfig = $this->orm->{'getChild' . ucfirst($child) . 'Config'}();

			foreach ($this->fillDifferentORM(new $childConfig->ormName()) as $orm) {
				$childs[$orm->{$orm->getAlias($childConfig->foreignKey)}][$orm->primaryKey] = $orm;
			}

			foreach ($childs as $id => $value) {
				$return[$id]->$child = $value;
			}
		}

		return $return;
	}

	/**
	 * Naplni jine ORM daty z vyhledavani
	 * @param AORM $orm
	 * @return array
	 */
	public function fillDifferentORM(AORM $orm)
	{
		return $orm->loadMultiple($this->results);
	}

	/**
	 * Vyresetuje knihovnu, aby provedla dalsi vyhledavani
	 */
	public function resetSearch()
	{
		$this->results = Array();
	}

	/**
	 * Vrati load query
	 * @return string
	 */
	protected function composeLoadQuery()
	{
		$query = 'SELECT ' . $this->getSelectCols() . ' FROM ' . $this->orm->getConfigDbTable() . ' ';
		$query .= ' ' . implode(' ', $this->joinTables);

		if (!empty($this->search)) {
			$query .= ' WHERE ' . implode($this->imploder, $this->search);
		}

		if (!empty($this->group)) {
			$query .= ' GROUP BY ' . implode(', ', $this->group);
		}

		if (!empty($this->ordering)) {
			$query .= ' ORDER BY ' . implode(', ', $this->ordering);
		}

		$query .= ' LIMIT ' . $this->offset . ', ' . $this->limit;
		return $query;
	}

	protected function getSelectCols()
	{
        return implode(', ', $this->getSelectColsInArray());
	}

    protected function getSelectColsInArray(){
        $return = Array();

        foreach ($this->selectCols as $cols) {
            foreach ($cols as $key => &$col) {
                if (isset($this->functionColumn[$col])) {
                    $col = $this->functionColumn[$col];
                }
            }
            $return = array_merge($return, $cols);
        }

        return $return;
    }

	public function addPager(\Listing\Pager_IPager $pager)
	{
		$this->offset = $pager->getOffset();
		$this->limit = $pager->getLimit();
	}

    /**
     * if used \PDO::FETCH_NUM method rename columns from numbers to format table.column
     * @param $results
     * @return mixed
     */
    public function renameFieldsFromFetchNum($results)
    {
        $cols = $this->getSelectColsInArray();
        $i = $j = 0;
        $return = array();
        foreach($results AS $result){
            foreach($result As $row){
                $return[$i][$cols[$j]] = $row;
                $j++;
            }
            $i++;
        }
        return $return;
    }


    public function getResultsWithChildsLoaded2()
    {
        $fetchType = \Query::FETCH_NUM;
        $queryBuilder = $this->orm->getQueryBuilder();
        $rows = $this->renameFieldsFromFetchNum($queryBuilder->loadByQuery($this->orm, $this->composeLoadQuery(), $this->params, $fetchType));
        dump($rows);

        $ormColumns = new Columns($this->orm);
        $ormColumns->fields = $this->orm->getAllDbFields();
        $ormColumns->aliases = $this->orm->getAllAliases();
        $ormColumns->objectName = $this->orm->getConfigObject();
        $ormColumns->primary = true;

        $columns = array();
        foreach($this->additionalOrms As $key => $additionalOrm){
            if($additionalOrm instanceof \ORM\Base){
                $additionalOrmColumns = new Columns($additionalOrm);
            } elseif($additionalOrm instanceof \Abstract_DataObjects) {
                $additionalOrmColumns = new ColumnsOldOrm($additionalOrm);
            } else {
                throw new \Exception('Unknown ORM search');
            }
            $additionalOrmColumns->name = $key;
            $additionalOrmColumns->fields = $additionalOrm->getAllDbFields();
            $additionalOrmColumns->aliases = $additionalOrm->getAllAliases();
            $additionalOrmColumns->objectName = $additionalOrm->getConfigObject();
            $columns[] = $additionalOrmColumns;
        }
        dump($columns);

        $results = array();
        foreach($rows As $row){
            /** @var \ORM\Base $primaryOrm */
            $primaryOrm = new $ormColumns->object;
            foreach($ormColumns->getColumns() AS $alias => $field){
                $primaryOrm->$alias = $row[$primaryOrm->getConfigDbTable().'.'.$field];
            }
            $children = array();
            foreach($columns AS $column){
                /** @var Columns|ColumnsOldOrm $column */
                $orm = new $column->object;
                foreach($column->getColumns() AS $alias => $field){
                    $orm->$alias = $row[$orm->getConfigDbTable().'.'.$field];
                }
                $children[] = $orm;
                $primaryOrm->{$column->name} = $children;
            }
            $results[] = $primaryOrm;
        }
//        dump($results);
//        dump($results[0]);
//        dump($results[0]->zone);
//        dump($results[0]->zone[0]);
//        die();
        return $results;
    }
}
