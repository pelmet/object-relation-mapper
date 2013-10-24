<?php

abstract class ObjectRelationMapper_DataExchange_Abstract
{
	/**
	 * @var ObjectRelationMapper_ORM
	 **/
	protected $orm;

	/**
	 * @inheritdoc
	 */
	public function __construct(ObjectRelationMapper_ORM $orm)
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