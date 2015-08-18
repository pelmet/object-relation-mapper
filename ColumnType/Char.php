<?php

namespace ObjectRelationMapper\ColumnType;

class Char extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return $this->col . ' CHAR(' . $this->length . ') ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		return ((is_string($value)) && (mb_strlen($value) == $this->length));
	}
}