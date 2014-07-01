<?php

namespace ObjectRelationMapper\DataExchange;

use ObjectRelationMapper\ORM;

abstract class AExchange
{
	/**
	 * @var ORM
	 **/
	protected $orm;

	/**
	 * @inheritdoc
	 */
	public function __construct(ORM $orm)
	{
		$this->orm = $orm;
	}

	protected $excluded = Array();

	/**
	 * Prida Excluded promennou
	 * @param $property
	 */
	public function addExclude($property)
	{
		$this->excluded[$property] = true;
	}

	/**
	 * Je promenna excluded?
	 * @param $property
	 * @return bool
	 */
	protected function isExcluded($property)
	{
		return isset($this->excluded[$property]);
	}
}