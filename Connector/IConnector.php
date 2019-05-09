<?php

namespace ObjectRelationMapper\Connector;

interface IConnector
{
	/**
	 * Runne query a vypise vysledek
	 * @param string $query
	 * @param array $parameters
	 * @param string $server
	 * @return mixed
	 */
	public function query($query, $parameters, $server);

    /**
     * Runne query z write serveru a vypise vysledek
     * @param string $query
     * @param array $parameters
     * @param string $server
     * @return mixed
     */
    public function queryWrite($query, $parameters, $server);

	/**
	 * Runne query a nevrati vysledek
	 * @param string $query
	 * @param array $parameters
	 * @param string $server
	 * @return mixed
	 */
	public function exec($query, $parameters, $server);
}