<?php

namespace ObjectRelationMapper;

/**
 * Class ColumnType_Interface
 * Interface pro ColType
 */
interface ColumnType_Interface
{
	public function validate($value);
	public function generateDbLine();
}