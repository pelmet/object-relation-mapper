<?php

namespace ObjectRelationMapper\ColumnType;

class Datetime extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return $this->col . ' DATETIME(' . $this->length . ') ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		$regexp = '/^\d{0,' . $this->length . '}$/';
		return (is_numeric($value) && (boolean)preg_match($regexp, (string)$value));
	}
}