<?php

namespace ObjectRelationMapper\ColumnType;

class Boolean extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return $this->col . ' INT(1) ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
        return is_bool(filter_var($value,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE));
	}
}