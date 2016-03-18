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
		$this->search[] = $this->dbFieldName($property) . ' != ' . $this->addParameter($value);
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
		$this->search[] = $this->dbFieldName($property) . ' <=> ' . $this->addParameter($value);
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
		$this->search[] = $this->dbFieldName($property) . ' >' . ($equals ? '=' : '') . ' ' . $this->addParameter($value);
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
		$this->search[] = $this->dbFieldName($property) . ' <' . ($equals ? '=' : '') . ' ' . $this->addParameter($value);
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
	 * Not like neco
	 * @param $property
	 * @param $value
	 * @return $this
	 */
	public function notLike($property, $value)
	{
		$this->search[] = $this->dbFieldName($property) . ' NOT LIKE ' . $this->addParameter($value);
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
		$this->search[] = $this->dbFieldName($property) . ' BETWEEN ' . $this->addParameter($min) . ' AND ' . $this->addParameter($max);
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
		$this->search[] = $this->addParameter($value) . ' BETWEEN ' . $this->dbFieldName($propertyFrom) . ' AND ' . $this->dbFieldName($propertyTo);
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

	public function addRandomOrdering()
	{
		$this->ordering[] = ' RAND() ';
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
	 * Prida childa s defaultnimi parametry
	 * @param $childName
	 * @param string $joinType
	 * @param array $additionalCols
	 * @param string $matching
	 * @return $this
	 */
	public function child($childName, $joinType = 'LEFT', $additionalCols = Array(), $matching = '=')
	{
		$this->addChild($childName, $joinType, $additionalCols, $matching);
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
		if ($value != NULL) {
			$this->addChild($child, 'LEFT OUTER', Array($property => $value), $matching);
		} else {
			$this->addChild($child, 'LEFT OUTER');
		}

		$this->search[] = $this->dbFieldName($child . '.' . $property) . ' IS NULL';
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
		$this->search[] =$this->dbFieldName($property) . ' IN (' . implode(',', $this->prepareInValues($values)) .')';
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
		$this->search[] = $this->dbFieldName($property) . ' NOT IN (' . implode(',', $this->prepareInValues($values)) .')';
		return $this;
	}

	/**
	 * pripravuje hodnoty pro PDO a vraci prepared nazvy
	 * @param array $values
	 * @return array
	 */
	private function prepareInValues(Array $values)
	{
		$preparedValues = array();
		foreach($values AS $value){
			$preparedValues[] = $this->addParameter($value);
		}
		return $preparedValues;
	}
}