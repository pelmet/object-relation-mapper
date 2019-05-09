<?php

namespace ObjectRelationMapper\Search;

class Search extends ASearch
{
	const ORDERING_DESCENDING = \ObjectRelationMapper\Base\AORM::ORDERING_DESCENDING;
	const ORDERING_ASCENDING = \ObjectRelationMapper\Base\AORM::ORDERING_ASCENDING;


	/**
	 * Hleda presnou neshodu
	 * @param $property
	 * @param $value
	 * @return $this
	 */
	public function notExact($property, $value)
	{
		$this->connector->notExact($property, $value);
		return $this;
	}

	/**
	 * Hleda presnou schodu
	 * @param $property
	 * @param $value
	 * @return $this
	 */
	public function exact($property, $value)
	{
	    $this->connector->exact($property, $value);
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
		$this->connector->from($property, $value, $equals);
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
	    $this->connector->to($property, $value, $equals);
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
	    $this->connector->like($property, $value);
		return $this;
	}

	/**
	 * Not like neco
	 * @param $property
	 * @param $value
	 * @return $this
	 */
	public function notLike($property, $value)
	{
	    $this->connector->notLike($property, $value);
		return $this;
	}

    /**
     * REGEXP pattern
     * @param string $property
     * @param string $pattern
     * @param bool $binary
     * @return $this
     */
    public function regexp($property, $pattern, $binary = false)
    {
        $this->connector->regexp($property, $pattern, $binary);
        return $this;
    }

	/**
	 * Hodnota sloupce BETWEEN min AND max (min <= expr AND expr <= max)
	 * @param $property
	 * @param $min
	 * @param $max
	 * @return $this
	 */
	public function propertyBetween($property, $min, $max)
	{
	    $this->connector->propertyBetween($property, $min, $max);
		return $this;
	}

	/**
	 * Hodnota BETWEEN col_1 AND col_2 (min <= expr AND expr <= max)
	 * @param $value
	 * @param $propertyFrom
	 * @param $propertyTo
	 * @return $this
	 */
	public function valueBetween($value, $propertyFrom, $propertyTo)
	{
	    $this->connector->valueBetween($value, $propertyFrom, $propertyTo);
		return $this;
	}

	/**
	 * Field je nulovy
	 * @param $property
	 * @return $this
	 */
	public function null($property)
	{
	    $this->connector->null($property);
		return $this;
	}

	/**
	 * Field je nenulovy
	 * @param $property
	 * @return $this
	 */
	public function notNull($property)
	{
	    $this->connector->notNull($property);
		return $this;
	}

	/**
	 * Prepne pouzivani OR misto AND mezi dotazy
	 * @return $this
	 */
	public function useOr()
	{
	    $this->connector->useOr();
		return $this;
	}

	/**
	 * Upravi Limit
	 * @param $limit
	 * @return $this
	 */
	public function limit($limit)
	{
	    $this->connector->limit($limit);
		return $this;
	}

	/**
	 * Upravi offset
	 * @param $offset
	 * @return $this
	 */
	public function offset($offset)
	{
	    $this->connector->offset($offset);
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
	    $this->connector->addOrdering($ordering, $direction);
		return $this;
	}

    /**
     * Prida random Ordering
     * @return $this
     */
	public function addRandomOrdering()
	{
	    $this->connector->addRandomOrdering();
	    return $this;
	}

    /**
     * Prida Field ordering
     * @param string $ordering
     * @param array $orderedValues
     * @return $this
     */
    public function addFieldOrdering($ordering, array $orderedValues)
    {
        $this->connector->addFieldOrdering($ordering, $orderedValues);
        return $this;
    }

	/**
	 * Prida column pro group by
	 * @param $property
	 * @return $this
	 */
	public function groupBy($property)
	{
	    $this->connector->groupBy($property);
		return $this;
	}

	/**
	 * Prida childa s defaultnimi parametry
	 * @param $childName
	 * @param string $joinType
	 * @param array $additionalCols
	 * @param string $matching
	 * @return $this
	 */
	public function child($childName, $joinType = 'LEFT', $additionalCols = Array(), $matching = '=')
	{
	    $this->connector->child($childName, $joinType, $additionalCols, $matching);
		return $this;
	}

	/**
	 * Vybere vsechny zaznamy ke kterym neexistuje child se zadanymi parametry nebo dany child obecne
	 * @param $child
	 * @param $property
	 * @param null $value
	 * @param string $matching
	 * @return $this
	 */
	public function notExist($child, $property, $value = NULL, $matching = '=')
	{
	    $this->connector->notExist($child, $property, $value, $matching);
		return $this;
	}

	/**
	 * Hleda presnou schodu pro sloupec v poli hodnot
	 * @param $property
	 * @param array $values
	 * @return $this
	 */
	public function in($property, Array $values)
	{
	    $this->connector->in($property, $values);
		return $this;
	}

	/**
	 * Hleda vse co neni v poli hodnot pro sloupec
	 * @param $property
	 * @param array $values
	 * @return $this
	 */
	public function notIn($property, Array $values)
	{
	    $this->connector->notIn($property, $values);
		return $this;
	}


}