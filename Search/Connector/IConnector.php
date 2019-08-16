<?php

namespace ObjectRelationMapper\Search\Connector;

interface IConnector
{
    const ORDERING_DESCENDING = \ObjectRelationMapper\Base\AORM::ORDERING_DESCENDING;
    const ORDERING_ASCENDING = \ObjectRelationMapper\Base\AORM::ORDERING_ASCENDING;
    
    /**
     * Hleda presnou neshodu
     * @param string $property
     * @param string $value
     * @return void
     */
    public function notExact($property, $value);

    /**
     * Hleda presnou schodu
     * @param string $property
     * @param string $value
     * @return void
     */
    public function exact($property, $value);

    /**
     * Vyhleda vse od
     * @param string $property
     * @param string $value
     * @param bool $equals
     * @return void
     */
    public function from($property, $value, $equals = true);

    /**
     * Vyhleda vse do
     * @param string $property
     * @param string $value
     * @param bool $equals
     * @return void
     */
    public function to($property, $value, $equals = true);

    /**
     * Like neco
     * @param string $property
     * @param string $value
     * @return void
     */
    public function like($property, $value);

    /**
     * Not like neco
     * @param string $property
     * @param string $value
     * @return void
     */
    public function notLike($property, $value);

    /**
     * REGEXP pattern
     * @param string $property
     * @param string $pattern
     * @param bool $binary
     * @return void
     */
    public function regexp($property, $pattern, $binary = false);

    /**
     * Hodnota sloupce BETWEEN min AND max (min <= expr AND expr <= max)
     * @param string $property
     * @param string $min
     * @param string $max
     * @return void
     */
    public function propertyBetween($property, $min, $max);

    /**
     * Hodnota BETWEEN col_1 AND col_2 (min <= expr AND expr <= max)
     * @param string $value
     * @param string $propertyFrom
     * @param string $propertyTo
     * @return void
     */
    public function valueBetween($value, $propertyFrom, $propertyTo);

    /**
     * Field je nulovy
     * @param string $property
     * @return void
     */
    public function null($property);

    /**
     * Field je nenulovy
     * @param string $property
     * @return void
     */
    public function notNull($property);

    /**
     * Prepne pouzivani OR misto AND mezi dotazy
     * @return void
     */
    public function useOr();

    /**
     * Upravi Limit
     * @param string $limit
     * @return void
     */
    public function limit($limit);

    /**
     * Upravi offset
     * @param string $offset
     * @return void
     */
    public function offset($offset);

    /**
     * Prida Ordering
     * @param string $ordering
     * @param string $direction
     * @return void
     */
    public function addOrdering($ordering, $direction = self::ORDERING_ASCENDING);

    /**
     * Prida Random ordering
     * @return void
     */
    public function addRandomOrdering();

    /**
     * Prida Field ordering
     * @param string $ordering
     * @param array $orderedValues
     * @return void
     */
    public function addFieldOrdering($ordering, array $orderedValues);

    /**
     * Prida column pro group by
     * @param string $property
     * @return void
     */
    public function groupBy($property);

    /**
     * Prida childa s defaultnimi parametry
     * @param string $childName
     * @param string $joinType
     * @param array $additionalCols
     * @param string $matching
     * @return void
     */
    public function child($childName, $joinType = 'LEFT', $additionalCols = Array(), $matching = '=');

    /**
     * Vybere vsechny zaznamy ke kterym neexistuje child se zadanymi parametry nebo dany child obecne
     * @param string $child
     * @param string $property
     * @param null $value
     * @param string $matching
     * @return void
     */
    public function notExist($child, $property, $value = NULL, $matching = '=');

    /**
     * Hleda presnou schodu pro sloupec v poli hodnot
     * @param string $property
     * @param array $values
     * @return void
     */
    public function in($property, Array $values);

    /**
     * Hleda vse co neni v poli hodnot pro sloupec
     * @param string $property
     * @param array $values
     * @return void
     */
    public function notIn($property, Array $values);

    /**
     * Slozi load query
     * @return string
     */
    public function composeLoadQuery();

    /**
     * Slozi count query
     * @return string
     */
    public function composeCountQuery();

    /**
     * Returns all params in array
     * @return array
     */
    public function getParams();

    /**
     * Return results in objects with children
     * @param array $rows
     * @return array
     */
    public function getResultsWithChildrenLoaded($rows = Array());

    /**
     * Return results in array
     * @return array
     */
    public function getResultsInArray();

    /**
     * Runs direct query on current orm connector and returns the results
     * @param string $query
     * @param array $params
     * @param int $fetchType
     * @return array
     */
    public function runCustomLoadQuery($query, Array $params, $fetchType = \PDO::FETCH_ASSOC);

    /**
     * Runs direct exec on current orm connector and returns the results
     * @param string $query
     * @param array $params
     * @return bool
     */
    public function runCustomExecQuery($query, Array $params);
}
