<?php

interface ObjectRelationMapper_DataExchange_Interface
{
	/**
	 * Zakladni Construct
	 * @param ObjectRelationMapper_ORM $orm
	 */
	public function __construct(ObjectRelationMapper_ORM $orm);

	/**
	 * Naplni ormko daty
	 * @param $data
	 * @return mixed
	 */
	public function load(Array $data);

	/**
	 * Exportuje data z ORMka do pole
	 * @return Array
	 */
	public function export();
}