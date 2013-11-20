<?php

namespace ObjectRelationMapper;

abstract class Search_Abstract
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

	/**
	 * Standardni construct
	 * @param ORM $orm
	 */
	public function __construct(ORM $orm)
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
		$this->params[] = Array(':param' .$this->searchCount, $value);
		return ':param' .$this->searchCount;
	}

	/**
	 * Prida childa, kdyz nechceme vyhledavat podle parametru
	 * @param $childName
	 */
	protected function addChild($childName)
	{
		$child = $this->orm->{'getChild'.ucfirst($childName).'Config'}();
		$orm = new $child->ormName();
		$this->additionalOrms[$childName] = $child;
		$this->joinTables[$orm->getConfigDbTable()] = ' LEFT JOIN '. $orm->getConfigDbTable().' ON '.$this->orm->getConfigDbTable().'.'.$child->localKey. ' = '.$orm->getConfigDbTable().'.'.$child->foreignKey.' ';
		$this->selectCols[$orm->getConfigDbTable()] = $orm->getAllDbFields(NULL, true);

		return $orm;
	}

	/**
	 * Prida DbField
	 * @param $field
	 * @return string
	 */
	protected function dbFieldName($field)
	{
		if(preg_match('/(.*)\.(.*)/', $field, $matches)){
            return $this->getOrmDbColumn($this->addChild($matches[1]), $matches[2]);
		} else {
            $this->aliasExists($field);
            return $this->getOrmDbColumn($this->orm, $field);
		}
	}

    /**
     * Vrati column z childa
     * @param ORM $orm
     * @param $alias
     * @return string
     */
    protected function getOrmDbColumn(ORM $orm, $alias)
    {
        return $orm->getDbField($alias, true);
    }

	/**
	 * Vrati, zda na ORM existuje Alias
	 * @param $property
	 * @throws Exception_ORM
	 */
	protected function aliasExists($property)
	{
		if(!in_array($property, $this->aliases)){
			throw new Exception_ORM('Alias '.$property.' neexistuje na ORM '. $this->orm->getConfigObject());
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
		$query = 'SELECT count('. $this->orm->getConfigDbPrimaryKey(). ') AS count FROM '.$this->orm->getConfigDbTable() . ' ';

        if(!empty($this->joinTables)){
            $query .= ' ' . implode(' ', $this->joinTables);
        }

		if(!empty($this->search)){
			$query .= ' WHERE ' .implode($this->imploder, $this->search);
		}

		return $query;
	}

	/**
	 * Vrati vsechny vysledky
	 * @return Array
	 */
	public function getResults()
	{
		if(empty($this->results)){
			$queryBuilder = $this->orm->getQueryBuilder();
			$this->results = $queryBuilder->loadByQuery($this->orm, $this->composeLoadQuery(), $this->params);
		}

		return $this->orm->loadMultiple($this->results);
	}

	/**
	 * Vrati vysledky s childama podle master ORM
	 * @return Array
	 */
	public function getResultsWithChildsLoaded()
	{
		$return = Array();

		foreach($this->getResults() as $orm){
			$return[$orm->primaryKey] = $orm;
		}

		foreach($this->additionalOrms as $child => $load){
			$childs = Array();
			foreach($this->fillDifferentORM(new $load->ormName()) as $orm){
				$childs[$orm->{$orm->getAlias($load->foreignKey)}][$orm->primaryKey] = $orm;
			}

			foreach($childs as $id => $value){
				$return[$id]->$child = $value;
			}
		}

		return $return;
	}

	/**
	 * Naplni jine ORM daty z vyhledavani
	 * @param ORM $orm
	 * @return array
	 */
	public function fillDifferentORM(ORM $orm)
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
		$query = 'SELECT '. $this->getSelectCols() . ' FROM '.$this->orm->getConfigDbTable() . ' ';
        $query .= ' ' . implode(' ', $this->joinTables);

		if(!empty($this->search)){
			$query .= ' WHERE ' .implode($this->imploder, $this->search);
		}

		if(!empty($this->group)){
			$query .= ' GROUP BY ' .implode(', ', $this->group);
		}

		if(!empty($this->ordering)){
			$query .= ' ORDER BY ' .implode(', ', $this->ordering);
		}

		$query .= ' LIMIT '.$this->offset .', '.$this->limit;

		return $query;
	}

	protected function getSelectCols()
	{
		$return = Array();

		foreach($this->selectCols as $cols){
			foreach($cols as $key => &$col){
				if(isset($this->functionColumn[$col])){
					$col = $this->functionColumn[$col];
				}
			}
			$return[] = implode(', ', $cols);
		}

		return implode(', ', $return);
	}
}