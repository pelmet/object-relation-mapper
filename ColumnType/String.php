<?php

namespace ObjectRelationMapper;

class ColumnType_String extends ColumnType_Abstract implements  ColumnType_Interface
{
	public function generateDbLine()
	{
		return $this->row . ' VARCHAR(' . $this->length . ') ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		return ((is_string($value)) && (mb_strlen($value) <= $this->length));
	}
}