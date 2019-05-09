<?php

namespace ObjectRelationMapper\ColumnType;

class CInt extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return 'integer';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param mixed $value
	 * @return bool
	 */
	public function validate($value)
	{
		$regexp = '/^\d{0,' . $this->length . '}$/';
		return (is_numeric($value) && (boolean)preg_match($regexp, (string)$value));
	}
}