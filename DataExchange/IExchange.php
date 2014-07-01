<?php

namespace ObjectRelationMapper\DataExchange;

use ObjectRelationMapper\ORM;

interface IExchange
{
	/**
	 * Zakladni Construct
	 * @param ORM $orm
	 */
	public function __construct(ORM $orm);

	/**
	 * Naplni ormko daty
	 * @param $data
	 * @return mixed
	 */
	public function load($data);

	/**
	 * Exportuje data z ORMka do pole
	 * @return Array
	 */
	public function export();
}