<?php

namespace ObjectRelationMapper\ColumnType;

class Date extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return $this->col . ' DATE';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
        //YYYY-MM-DD from '1000-01-01' to '9999-12-31'
		return (is_string($value) && preg_match('/^[1-9][0-9]{3}-(([0][1-9])|([1][0-2]))-(([0][1-9])|([12][0-9])|([3][01]))$/',$value));
	}
}