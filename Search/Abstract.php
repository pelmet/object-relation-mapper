<?php

abstract class ObjectRelationMapper_Search_Abstract
{

	/**
	 * @var ObjectRelationMapper_ORM
	 */
	protected $orm;
	protected $search = Array();
	protected $params = Array();
	protected $joinTables = Array();
	protected $aliases = Array();
	protected $searchCount = 0;
	protected $query;
	protected $countQuery;
	protected $limit = 999999;
	protected $offset = 0;
	protected $imploder = ' AND ';
	protected $ordering = Array();

	/**
	 * Standardni construct
	 * @param ObjectRelationMapper_ORM $orm
	 */
	public function __construct(ObjectRelationMapper_ORM $orm)
	{
		$this->orm = $orm;
		$this->aliases = $orm->getAllAliases();
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
	 * Prida DbField
	 * @param $field
	 * @return string
	 */
	protected function dbFieldName($field)
	{
		if(preg_match('/(.*)\.(.*)/', $field, $matches)){
			$child = $this->orm->{'getChild'.ucfirst($matches[1]).'Config'}();
            $orm = new $child->ormName();
            $this->joinTables[] = ' LEFT JOIN '.$orm->getConfigDbTable().' ON '.$this->orm->getConfigDbTable().'.'.$child->localKey. ' = '.$orm->getConfigDbTable().'.'.$child->foreignKey.' ';
            return $this->getOrmDbColumn($orm, $matches[2]);
		} else {
            $this->aliasExists($field);
            return $this->getOrmDbColumn($this->orm, $field);
		}
	}

    /**
     * Vrati column z childa
     * @param ObjectRelationMapper_ORM $orm
     * @param $alias
     * @return string
     */
    protected function getOrmDbColumn(ObjectRelationMapper_ORM $orm, $alias)
    {
        return $orm->getDbField($alias, true);
    }

	/**
	 * Vrati, zda na ORM existuje Alias
	 * @param $property
	 * @throws ObjectRelationMapper_Exception_ORM
	 */
	protected function aliasExists($property)
	{
		if(!in_array($property, $this->aliases)){
			throw new ObjectRelationMapper_Exception_ORM('Alias '.$property.' neexistuje na ORM '. $this->orm->getConfigObject());
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
		return $this->orm->loadByQuery($this->composeLoadQuery(), $this->params);
	}

	/**
	 * Vrati load query
	 * @return string
	 */
	protected function composeLoadQuery()
	{
		$query = 'SELECT '. $this->orm->getAllDbFields(', ', true). ' FROM '.$this->orm->getConfigDbTable() . ' ';

        if(!empty($this->joinTables)){
            $query .= ' ' . implode(' ', $this->joinTables);
        }

		if(!empty($this->search)){
			$query .= ' WHERE ' .implode($this->imploder, $this->search);
		}

		if(!empty($this->ordering)){
			$query .= ' ORDER BY ' .implode($this->imploder, $this->ordering);
		}

		$query .= ' LIMIT '.$this->offset .', '.$this->limit;

		return $query;
	}
}