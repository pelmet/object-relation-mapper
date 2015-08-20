<?php

namespace ObjectRelationMapper\ColumnType;

class Text extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return $this->col . ' TEXT ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		return (is_string($value));
	}
}