<?php

namespace ObjectRelationMapper\Search\Connector;

class SQLite extends DB
{
    /**
     * Hleda presnou schodu
     * @param string $property
     * @param string $value
     * @return void
     */
    public function exact($property, $value)
    {
        $this->search[] = $this->dbFieldName($property) . ' IS ' . $this->addParameter($value);
    }

    /**
     * Prida Field ordering
     * @param string $ordering
     * @param array $orderedValues
     */
    public function addFieldOrdering($ordering, array $orderedValues)
    {
        $params = [];
        foreach ($orderedValues as $index => $value) {
            $params[] = ' WHEN ' . $this->addParameter($value) . ' THEN ' . $index;
        }
        $this->ordering[] = ' CASE '.$this->dbFieldName($ordering)." " . implode(" ", $params)." END";
    }
}