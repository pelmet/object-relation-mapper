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
}