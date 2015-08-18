<?php

namespace ObjectRelationMapper\ColumnType;

/**
 * Class Enum
 * Enumerated column
 * @package ObjectRelationMapper\ColumnType
 *
 * Usage:
$this->addColumn('column_name', 'ormColumnName', 'enum', 'stringLength', ['values' => ['value1','value2','value3',...]]);
 * Example:
$this->addColumn('m_status', 'status', 'enum', 255, ['values' => ['unknown','active','retired','deceased']]);
 */
class Enum extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		$enumerated = implode("','", $this->getValues());
		$enumerated = ($enumerated == '') ? '' : "'" . $enumerated . "'" ;
		return $this->col . ' ENUM(' . $enumerated . ') ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		return (is_string($value) && in_array($value, $this->getValues()));
	}

	/**
	 * Get all allowed values
	 * @return array of strings
	 */
	public function getValues()
	{
		return array_column($this->additionalParams, 'values');
	}

	/**
	 * Set all allowed values at once
	 * could also simply remove all values by set an empty array
	 * @param $values array of strings
	 */
	public function setValues($values = array())
	{
		$this->additionalParams['values'] = $values;
	}
}