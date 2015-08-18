<?php

namespace ObjectRelationMapper\ColumnType;

class Bigint extends Int
{
	public function generateDbLine()
	{
		return $this->col . ' BIGINT(' . $this->length . ') ';
	}
}