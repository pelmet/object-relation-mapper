<?php

interface ObjectRelationMapper_Connector_Interface
{
	/**
	 * Runne query a vypise vysledek
	 * @param $query
	 * @param $parameters
	 * @param $server
	 * @return mixed
	 */
	public function query($query, $parameters, $server);

	/**
	 * Runne query a nevrati vysledek
	 * @param $query
	 * @param $parameters
	 * @param $server
	 * @return mixed
	 */
	public function exec($query, $parameters, $server);
}