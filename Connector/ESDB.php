<?php

namespace ObjectRelationMapper\Connector;

class ESDB implements IConnector
{
	protected $db;

	public function __construct($db = NULL)
	{
		if ($db != NULL) {
			$this->db = $db;
		} else {
			$this->db = \Factory::Db();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function query($query, $parameters, $server, $fetchType = \DB::FETCH_ASSOC)
	{
		return $this->db->query($query)->par($parameters)->s($server)->fetch($fetchType)->ex();
	}

    /**
     * @inheritdoc
     */
	public function queryWrite($query, $parameters, $server, $fetchType = \DB::FETCH_ASSOC)
    {
        return $this->db->query($query)->par($parameters)->s($server)->fetch($fetchType)->fromWrite()->ex();
    }

	/**
	 * @inheritdoc
	 */
	public function exec($query, $parameters, $server)
	{
		return $this->db->exec($query)->par($parameters)->s($server)->ex();
	}
}