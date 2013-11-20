<?php

namespace ObjectRelationMapper;

class Search_Search extends Search_Abstract
{
    const ORDERING_DESCENDING = ORM::ORDERING_DESCENDING;
    const ORDERING_ASCENDING = ORM::ORDERING_ASCENDING;


    /**
     * Hleda presnou schodu
     * @param $property
     * @param $value
     * @return $this
     */
    public function exact($property, $value)
    {
        $this->search[] = $this->dbFieldName($property) . ' = ' . $this->addParameter($value);
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
        $this->search[] = $this->dbFieldName($property) . ' >'.($equals ? '=' : '').' ' . $this->addParameter($value);
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
        $this->search[] = $this->dbFieldName($property) . ' <'.($equals ? '=' : '').' ' . $this->addParameter($value);
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
        $this->search[] = $this->dbFieldName($property) . ' LIKE ' . $this->addParameter($value);
        return $this;
    }

    /**
     * Field je nulovy
     * @param $property
     * @return $this
     */
    public function null($property)
    {
        $this->search[] = $this->dbFieldName($property) . ' IS NULL';
        return $this;
    }

    /**
     * Field je nenulovy
     * @param $property
     * @return $this
     */
    public function notNull($property)
    {
        $this->search[] = $this->dbFieldName($property) . ' IS NOT NULL';
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
        $this->ordering[] = $this->dbFieldName($ordering) . ' ' . $direction;
        return $this;
    }

	/**
	 * Prida column pro group by
	 * @param $property
	 * @return $this
	 */
	public function groupBy($property)
	{
		$this->group[] = $this->dbFieldName($property);
		return $this;
	}

	/**
	 * Prida mysql funkci k property a vybere ji pak
	 * @param $property
	 * @param $function
	 * @notyetimplemented
	 */
	/*public function selectFunctionColumn($property, $function)
	{
		$this->functionColumn[$this->dbFieldName($property)] = $function . '(' . $this->dbFieldName($property) . ') AS ' .$this->dbFieldName($property) ;
	}*/
}