<?php

namespace ObjectRelationMapper\ColumnType;

class CString extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return 'string';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param string $value
	 * @return bool
	 */
	public function validate($value)
	{
		return ((is_string($value)) && (mb_strlen($value) <= $this->length));
	}
}