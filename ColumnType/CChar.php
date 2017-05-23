<?php

namespace ObjectRelationMapper\ColumnType;

/**
 * Class Char
 * @package ObjectRelationMapper\ColumnType
 * Column type Characters -> needs string length as exactly as defined (unlike usual string)
 */
class CChar extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return 'string';
	}

	/**
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		return ((is_string($value)) && (mb_strlen($value) == $this->length));
	}
}