<?php

namespace ObjectRelationMapper\ColumnType;

class Enum extends AColumn implements IColumn
{
    public function generateDbLine()
    {
        return $this->col . ' ENUM(' . implode(',', $this->length) . ') ';
    }

    /**
     * Zvaliduje danou hodnotu a vrati true/false
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        return (in_array($value, $this->length));
    }
}