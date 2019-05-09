<?php

namespace ObjectRelationMapper\ColumnType;

class CText extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return 'text';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param string $value
	 * @return bool
	 */
	public function validate($value)
	{
		return (is_string($value));
	}
}