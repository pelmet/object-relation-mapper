<?php

namespace ObjectRelationMapper\ColumnType;

class CDecimal extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return 'decimal';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param string $value
	 * @return bool
	 */
	public function validate($value)
	{
		return (is_numeric($value) && $this->checkDecimalLength($value));
	}

	private function checkDecimalLength($value)
	{
		if (preg_match('/^([0-9]+)[\.,;\/\\\:]{1}([0-9]+)$/i', $this->length, $matches) && !preg_match('/^[0-9]{0,' . $matches[1] . '}([\.,]{1}[0-9]{0,' . $matches[2] . '})?$/', (string)$value)) {
			return false;
		}
		return true;
	}
}