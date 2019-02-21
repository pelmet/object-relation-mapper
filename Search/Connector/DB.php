<?php

namespace ObjectRelationMapper\Search\Connector;

class DB extends AConnector implements IConnector
{
    /**
     * Vrati text count query
     * @return string
     */
    public function composeCountQuery()
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
     * Vrati load query
     * @return string
     */
    public function composeLoadQuery()
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

    /**
     * Hleda presnou neshodu
     * @param $property
     * @param $value
     * @return void
     */
    public function notExact($property, $value)
    {
        $this->search[] = $this->dbFieldName($property) . ' != ' . $this->addParameter($value);
    }

    /**
     * Hleda presnou schodu
     * @param $property
     * @param $value
     * @return void
     */
    public function exact($property, $value)
    {
        $this->search[] = $this->dbFieldName($property) . ' <=> ' . $this->addParameter($value);
    }

    /**
     * Vyhleda vse od
     * @param $property
     * @param $value
     * @param bool $equals
     * @return void
     */
    public function from($property, $value, $equals = true)
    {
        $this->search[] = $this->dbFieldName($property) . ' >' . ($equals ? '=' : '') . ' ' . $this->addParameter($value);
    }

    /**
     * Vyhleda vse do
     * @param $property
     * @param $value
     * @param bool $equals
     * @return void
     */
    public function to($property, $value, $equals = true)
    {
        $this->search[] = $this->dbFieldName($property) . ' <' . ($equals ? '=' : '') . ' ' . $this->addParameter($value);
    }

    /**
     * Like neco
     * @param $property
     * @param $value
     * @return void
     */
    public function like($property, $value)
    {
        $this->search[] = $this->dbFieldName($property) . ' LIKE ' . $this->addParameter($value);
    }

    /**
     * Not like neco
     * @param $property
     * @param $value
     * @return void
     */
    public function notLike($property, $value)
    {
        $this->search[] = $this->dbFieldName($property) . ' NOT LIKE ' . $this->addParameter($value);
    }

    /**
     * Hodnota sloupce BETWEEN min AND max (min <= expr AND expr <= max)
     * @param $property
     * @param $min
     * @param $max
     * @return void
     */
    public function propertyBetween($property, $min, $max)
    {
        $this->search[] = $this->dbFieldName($property) . ' BETWEEN ' . $this->addParameter($min) . ' AND ' . $this->addParameter($max);
    }

    /**
     * Hodnota BETWEEN col_1 AND col_2 (min <= expr AND expr <= max)
     * @param $value
     * @param $propertyFrom
     * @param $propertyTo
     * @return void
     */
    public function valueBetween($value, $propertyFrom, $propertyTo)
    {
        $this->search[] = $this->addParameter($value) . ' BETWEEN ' . $this->dbFieldName($propertyFrom) . ' AND ' . $this->dbFieldName($propertyTo);
    }

    /**
     * Field je nulovy
     * @param $property
     * @return void
     */
    public function null($property)
    {
        $this->search[] = $this->dbFieldName($property) . ' IS NULL';
    }

    /**
     * Field je nenulovy
     * @param $property
     * @return void
     */
    public function notNull($property)
    {
        $this->search[] = $this->dbFieldName($property) . ' IS NOT NULL';
    }

    /**
     * Prepne pouzivani OR misto AND mezi dotazy
     * @return void
     */
    public function useOr()
    {
        $this->imploder = ' OR ';
    }

    /**
     * Upravi Limit
     * @param $limit
     * @return void
     */
    public function limit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Upravi offset
     * @param $offset
     * @return void
     */
    public function offset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Prida Ordering
     * @param $ordering
     * @param string $direction
     * @return void
     */
    public function addOrdering($ordering, $direction = self::ORDERING_ASCENDING)
    {
        $this->ordering[] = $this->dbFieldName($ordering) . ' ' . $direction;
    }

    public function addRandomOrdering()
    {
        $this->ordering[] = ' RAND() ';
    }

    /**
     * Prida Field ordering
     * @param string $ordering
     * @param array $orderedValues
     */
    public function addFieldOrdering($ordering, array $orderedValues)
    {
        $params = [];
        foreach ($orderedValues as $value) {
            $params[] = $this->addParameter($value);
        }
        $this->ordering[] = ' FIELD('.$this->dbFieldName($ordering).','.implode(',', $params).')';
    }

    /**
     * Prida column pro group by
     * @param $property
     * @return void
     */
    public function groupBy($property)
    {
        $this->group[] = $this->dbFieldName($property);
    }

    /**
     * Prida childa s defaultnimi parametry
     * @param $childName
     * @param string $joinType
     * @param array $additionalCols
     * @param string $matching
     * @return void
     */
    public function child($childName, $joinType = 'LEFT', $additionalCols = Array(), $matching = '=')
    {
        $this->addChild($childName, $joinType, $additionalCols, $matching);
    }

    /**
     * Vybere vsechny zaznamy ke kterym neexistuje child se zadanymi parametry nebo dany child obecne
     * @param $child
     * @param $property
     * @param null $value
     * @param string $matching
     * @return void
     */
    public function notExist($child, $property, $value = NULL, $matching = '=')
    {
        if ($value != NULL) {
            $this->addChild($child, 'LEFT OUTER', Array($property => $value), $matching);
        } else {
            $this->addChild($child, 'LEFT OUTER');
        }

        $this->search[] = $this->dbFieldName($child . '.' . $property) . ' IS NULL';
    }

    /**
     * Hleda presnou schodu pro sloupec v poli hodnot
     * @param $property
     * @param array $values
     * @return void
     */
    public function in($property, Array $values)
    {
        $this->search[] =$this->dbFieldName($property) . ' IN (' . implode(',', $this->prepareInValues($values)) .')';
    }

    /**
     * Hleda vse co neni v poli hodnot pro sloupec
     * @param $property
     * @param array $values
     * @return void
     */
    public function notIn($property, Array $values)
    {
        $this->search[] = $this->dbFieldName($property) . ' NOT IN (' . implode(',', $this->prepareInValues($values)) .')';
    }
}