<?php

namespace ObjectRelationMapper;

class ColumnType_Int extends ColumnType_Abstract implements ColumnType_Interface
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