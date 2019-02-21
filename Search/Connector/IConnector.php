<?php

namespace ObjectRelationMapper\Search\Connector;

interface IConnector
{
    const ORDERING_DESCENDING = \ObjectRelationMapper\Base\AORM::ORDERING_DESCENDING;
    const ORDERING_ASCENDING = \ObjectRelationMapper\Base\AORM::ORDERING_ASCENDING;
    
    /**
     * Hleda presnou neshodu
     * @param $property
     * @param $value
     * @return void
     */
    public function notExact($property, $value);

    /**
     * Hleda presnou schodu
     * @param $property
     * @param $value
     * @return void
     */
    public function exact($property, $value);

    /**
     * Vyhleda vse od
     * @param $property
     * @param $value
     * @param bool $equals
     * @return void
     */
    public function from($property, $value, $equals = true);

    /**
     * Vyhleda vse do
     * @param $property
     * @param $value
     * @param bool $equals
     * @return void
     */
    public function to($property, $value, $equals = true);

    /**
     * Like neco
     * @param $property
     * @param $value
     * @return void
     */
    public function like($property, $value);

    /**
     * Not like neco
     * @param $property
     * @param $value
     * @return void
     */
    public function notLike($property, $value);

    /**
     * Hodnota sloupce BETWEEN min AND max (min <= expr AND expr <= max)
     * @param $property
     * @param $min
     * @param $max
     * @return void
     */
    public function propertyBetween($property, $min, $max);

    /**
     * Hodnota BETWEEN col_1 AND col_2 (min <= expr AND expr <= max)
     * @param $value
     * @param $propertyFrom
     * @param $propertyTo
     * @return void
     */
    public function valueBetween($value, $propertyFrom, $propertyTo);

    /**
     * Field je nulovy
     * @param $property
     * @return void
     */
    public function null($property);

    /**
     * Field je nenulovy
     * @param $property
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
     * @param $limit
     * @return void
     */
    public function limit($limit);

    /**
     * Upravi offset
     * @param $offset
     * @return void
     */
    public function offset($offset);

    /**
     * Prida Ordering
     * @param $ordering
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
     * @param $property
     * @return void
     */
    public function groupBy($property);

    /**
     * Prida childa s defaultnimi parametry
     * @param $childName
     * @param string $joinType
     * @param array $additionalCols
     * @param string $matching
     * @return void
     */
    public function child($childName, $joinType = 'LEFT', $additionalCols = Array(), $matching = '=');

    /**
     * Vybere vsechny zaznamy ke kterym neexistuje child se zadanymi parametry nebo dany child obecne
     * @param $child
     * @param $property
     * @param null $value
     * @param string $matching
     * @return void
     */
    public function notExist($child, $property, $value = NULL, $matching = '=');

    /**
     * Hleda presnou schodu pro sloupec v poli hodnot
     * @param $property
     * @param array $values
     * @return void
     */
    public function in($property, Array $values);

    /**
     * Hleda vse co neni v poli hodnot pro sloupec
     * @param $property
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
     * @return Array
     */
    public function getParams();

    /**
     * Return results in objects with children
     * @return array
     */
    public function getResultsWithChildrenLoaded();

    /**
     * Return results in array
     * @return array
     */
    public function getResultsInArray();
}