<?php

/**
 * Class ObjectRelationMapper_ColumnType_Interface
 * Interface pro ColType
 */
interface ObjectRelationMapper_ColumnType_Interface
{
	public function validate($value);
	public function generateDbLine();
}