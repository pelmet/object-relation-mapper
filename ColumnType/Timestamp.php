<?php

namespace ObjectRelationMapper\ColumnType;

/**
 * Class Timestamp
 * Time column, not integer timestamp
 * @package ObjectRelationMapper\ColumnType
 */
class Timestamp extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return $this->col . ' TIMESTAMP ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		$regexp = '/^(((\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12][0-9]|3[01]))|((\d{4})(-)(0[469]|11)(-)([0][1-9]|[12][0-9]|30))|((\d{4})(-)(02)(-)(0[1-9]|1[0-9]|2[0-8]))|(([02468][048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|(([0-9][0-9][0][48])(-)(02)(-)(29))|(([0-9][0-9][2468][048])(-)(02)(-)(29))|(([0-9][0-9][13579][26])(-)(02)(-)(29))) ((([01][0-9])|(2[0123])):[0-5][0-9]:[0-5][0-9])$/iu';
		if (!(is_string($value) || is_numeric($value))) {
			return false;
		}
		return ((boolean)preg_match($regexp, (string)$value));
	}
}