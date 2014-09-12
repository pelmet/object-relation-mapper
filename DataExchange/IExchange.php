<?php

namespace ObjectRelationMapper\DataExchange;

use ObjectRelationMapper\Base\AORM;

interface IExchange
{
	/**
	 * Zakladni Construct
	 * @param AORM $orm
	 */
	public function __construct(AORM $orm);

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