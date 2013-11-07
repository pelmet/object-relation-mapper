<?php

class ObjectRelationMapper_ColumnType_Decimal extends ObjectRelationMapper_ColumnType_Abstract implements ObjectRelationMapper_ColumnType_Interface
{
	public function generateDbLine()
	{
		return $this->row . ' DECIMAL(' . $this->length . ') ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
        return (is_numeric($value) && $this->checkDecimalLength($value));
	}

    private function checkDecimalLength($value)
    {
        if(preg_match('/^([0-9]+)[\.,;\/\\\:]{1}([0-9]+)$/i',$this->length,$matches) && !preg_match('/^[0-9]{0,'.$matches[1].'}([\.,]{1}[0-9]{0,'.$matches[2].'})?$/',(string)$value)){
            return false;
        }
        return true;
    }
}