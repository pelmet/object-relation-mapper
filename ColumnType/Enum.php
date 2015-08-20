<?php

namespace ObjectRelationMapper\ColumnType;

/**
 * Class Enum
 * Enumerated column
 * @package ObjectRelationMapper\ColumnType
 *
 * Usage:
$this->addColumn('column_name', 'ormColumnName', 'enum', ['value1','value2','value3',...]);
 * Example:
$this->addColumn('m_status', 'status', 'enum', ['unknown','active','retired','deceased']);
 */
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