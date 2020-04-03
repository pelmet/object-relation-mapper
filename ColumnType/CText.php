<?php

namespace ObjectRelationMapper\ColumnType;

class CText extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return 'text';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 *
	 * @param string $value
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
