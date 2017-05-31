<?php

namespace ObjectRelationMapper\Search\Connector;

class SQLite extends DB
{
    /**
     * Hleda presnou schodu
     * @param $property
     * @param $value
     * @return void
     */
    public function exact($property, $value)
    {
        $this->search[] = $this->dbFieldName($property) . ' = ' . $this->addParameter($value);
    }
}