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
	 * @param mixed $value
	 * @return bool
	 */
	public function validate($value)
	{
		$canBeConvertedToString = is_scalar($value) || (
				is_object($value) && method_exists($value, '__toString')
			);
		return ($canBeConvertedToString && (mb_strlen((string)$value) == $this->length));
	}
}
