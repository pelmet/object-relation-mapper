<?php

namespace ObjectRelationMapper\ColumnType;

/**
 * Class IColumn
 * Interface pro ColType
 */
interface IColumn
{
	public function validate($value);

	public function generateDbLine();

	public function getSanitezedPDOValue($value);
}