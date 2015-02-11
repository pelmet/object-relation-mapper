<?php

namespace ObjectRelationMapper\Connector;

class Database implements IConnector
{
	public function __construct()
	{

	}

	/**
	 * @inheritdoc
	 */
	public function query($query, $parameters, $server, $fetchType = \Query::FETCH_ASSOC)
	{
        $q = new \Query();
        return $q->query($query)->par($parameters)->s($server)->fetch($fetchType)->ex();
	}

	/**
	 * @inheritdoc
	 */
	public function exec($query, $parameters, $server)
	{
		$e = new \Exec();
		return $e->query($query)->par($parameters)->s($server)->ex();
	}
}