<?php

namespace ObjectRelationMapper\Connector;

class ESDB implements IConnector
{
	protected $db;

	public function __construct($db = NULL)
	{
		if (!is_null($db)) {
			$this->db = $db;
		} else {
			$this->db = \Factory::Db();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function query($query, $parameters, $server)
	{
		return $this->db->query($query)->par($parameters)->s($server)->fetch(\Db::FETCH_ASSOC)->ex();
	}

	/**
	 * @inheritdoc
	 */
	public function exec($query, $parameters, $server)
	{
		return $this->db->exec($query)->par($parameters)->s($server)->ex();
	}
}