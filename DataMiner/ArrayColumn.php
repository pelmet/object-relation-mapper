<?php

namespace ObjectRelationMapper\DataMiner;

class ArrayColumn
{
	/**
	 * @var \ObjectRelationMapper\ORM
	 **/
	protected $orm;

	/**
	 * @inheritdoc
	 */
	public function process(Array $input, $column)
	{
		$result = Array();

		foreach ($input as $index => $orm){
			$result[] = $orm->{$column};
		}

		return $result;
	}
}