<?php

namespace ObjectRelationMapper\ColumnType;

class CString extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return 'string';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public function validate($value)
	{
		$canBeConvertedToString = is_scalar($value) || (
				is_object($value) && method_exists($value, '__toString')
			);
		return ($canBeConvertedToString && (mb_strlen((string)$value) <= $this->length));
	}
}
