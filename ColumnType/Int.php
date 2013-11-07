<?php

class ObjectRelationMapper_ColumnType_Int extends ObjectRelationMapper_ColumnType_Abstract implements ObjectRelationMapper_ColumnType_Interface
{
	public function generateDbLine()
	{
		return $this->row . ' INT(' . $this->length . ') ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
        $regexp = '/^\d{0,'.$this->length.'}$/';
        return (is_numeric($value) && (boolean)preg_match($regexp,(string)$value));
	}
}