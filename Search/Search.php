<?php

class ObjectRelationMapper_Search_Search
{
    const ORDERING_DESCENDING = ObjectRelationMapper_ORM::ORDERING_DESCENDING;
    const ORDERING_ASCENDING = ObjectRelationMapper_ORM::ORDERING_ASCENDING;

    protected $aliases;
    /**
     * @var ObjectRelationMapper_ORM
     */
    protected $orm;

    protected $search = Array();
    protected $params = Array();

    protected $searchCount = 0;

    protected $imploder = ' AND ';

    protected $limit = 999999;
    protected $offset = 0;

    protected $ordering = Array();

    public function __construct(ObjectRelationMapper_ORM $orm)
    {
        $this->orm = $orm;
        $this->aliases = $orm->getAllAliases();
    }

	public function getCount()
	{
		$query = 'SELECT count('. $this->orm->getConfigDbPrimaryKey(). ') AS count FROM '.$this->orm->getConfigDbTable() . ' ';

		if(!empty($this->search)){
			$query .= ' WHERE ' .implode($this->imploder, $this->search);
		}

		return $this->orm->countByQuery($query, $this->params);
	}

    /**
     * Vrati vsechny vysledky
     * @return Array
     */
    public function getResults()
    {
        $query = 'SELECT '. $this->orm->getAllDbFields(', ', true). ' FROM '.$this->orm->getConfigDbTable() . ' ';

        if(!empty($this->search)){
            $query .= ' WHERE ' .implode($this->imploder, $this->search);
        }

        if(!empty($this->ordering)){
            $query .= ' ORDER BY ' .implode($this->imploder, $this->search);
        }

        $query .= ' LIMIT '.$this->offset .', '.$this->limit;

        return $this->orm->loadByQuery($query, $this->params);
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
     * Hleda presnou schodu
     * @param $property
     * @param $value
     * @return $this
     */
    public function exact($property, $value)
    {
        $this->aliasExists($property);

        $this->search[] = $this->orm->getDbField($property, true) . ' = :param' .$this->searchCount;
        $this->params[] = Array(':param' .$this->searchCount, $value);

        $this->searchCount++;
        return $this;
    }

    /**
     * Vyhleda vse od
     * @param $property
     * @param $value
     * @param bool $equals
     * @return $this
     */
    public function from($property, $value, $equals = true)
    {
        $this->aliasExists($property);

        $this->search[] = $this->orm->getDbField($property, true) . ' >'.($equals ? '=' : '').' :param' .$this->searchCount;
        $this->params[] = Array(':param' .$this->searchCount, $value);

        $this->searchCount++;
        return $this;
    }

    /**
     * Vyhleda vse do
     * @param $property
     * @param $value
     * @param bool $equals
     * @return $this
     */
    public function to($property, $value, $equals = true)
    {
        $this->aliasExists($property);

        $this->search[] = $this->orm->getDbField($property, true) . ' <'.($equals ? '=' : '').' :param' .$this->searchCount;
        $this->params[] = Array(':param' .$this->searchCount, $value);

        $this->searchCount++;
        return $this;
    }

    /**
     * Like neco
     * @param $property
     * @param $value
     * @return $this
     */
    public function like($property, $value)
    {
        $this->aliasExists($property);

        $this->search[] = $this->orm->getDbField($property, true) . ' LIKE :param' .$this->searchCount;
        $this->params[] = Array(':param' .$this->searchCount, $value);

        $this->searchCount++;
        return $this;
    }

    /**
     * Field je nulovy
     * @param $property
     * @return $this
     */
    public function null($property)
    {
        $this->aliasExists($property);

        $this->search[] = $this->orm->getDbField($property, true) . ' IS NULL';

        $this->searchCount++;
        return $this;
    }

    /**
     * Field je nenulovy
     * @param $property
     * @return $this
     */
    public function notNull($property)
    {
        $this->aliasExists($property);

        $this->search[] = $this->orm->getDbField($property) . ' IS NOT NULL';

        $this->searchCount++;
        return $this;
    }

    /**
     * Prepne pouzivani OR misto AND mezi dotazy
     * @return $this
     */
    public function useOr()
    {
        $this->imploder = ' OR ';

        return $this;
    }

    /**
     * Upravi Limit
     * @param $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;

    }

    /**
     * Upravi offset
     * @param $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;

    }

    /**
     * Prida Ordering
     * @param $ordering
     * @param string $direction
     * @return $this
     */
    public function addOrdering($ordering, $direction = self::ORDERING_ASCENDING)
    {
        $this->ordering[] = $this->orm->getDbField($ordering) . ' ' . $direction;
        return $this;
    }
}