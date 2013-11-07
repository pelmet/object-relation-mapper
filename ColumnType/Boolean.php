<?php

class ObjectRelationMapper_ColumnType_Boolean extends ObjectRelationMapper_ColumnType_Abstract implements ObjectRelationMapper_ColumnType_Interface
{
	public function generateDbLine()
	{
		return $this->row . ' INT(1) ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
        return (is_bool($value) || (!is_array($value) && !is_object($value) && (boolean)preg_match('/(^(0|1)$)|(^(T|F)$)|(^(TRUE|FALSE)$)/i',(string)$value)));
	}
}