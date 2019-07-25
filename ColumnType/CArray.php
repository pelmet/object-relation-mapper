<?php

namespace ObjectRelationMapper\ColumnType;

class CArray extends AColumn implements IColumn
{
    /**
     * @throws \ObjectRelationMapper\Exception\ORM
     */
    public function generateDbLine()
    {
       throw new \ObjectRelationMapper\Exception\ORM('DB CANT IMPLEMENT ARRAY VALUE TYPE');
    }

    /**
     * Zvaliduje danou hodnotu a vrati true/false
     * @param mixed $value
     * @return bool
     */
    public function validate($value)
    {
        return is_array($value);
    }
}