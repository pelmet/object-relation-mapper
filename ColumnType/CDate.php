<?php

namespace ObjectRelationMapper\ColumnType;

class CDate extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return 'date';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		//YYYY-MM-DD from '1000-01-01' to '9999-12-31'
		//$regexp = '/^(((\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12][0-9]|3[01]))|((\d{4})(-)(0[469]|11)(-)([0][1-9]|[12][0-9]|30))|((\d{4})(-)(02)(-)(0[1-9]|1[0-9]|2[0-8]))|(([02468][048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|(([0-9][0-9][0][48])(-)(02)(-)(29))|(([0-9][0-9][2468][048])(-)(02)(-)(29))|(([0-9][0-9][13579][26])(-)(02)(-)(29)))$/iu';
		return (is_string($value) && preg_match('/^[1-9][0-9]{3}-(([0][1-9])|([1][0-2]))-(([0][1-9])|([12][0-9])|([3][01]))$/',$value));
	}
}