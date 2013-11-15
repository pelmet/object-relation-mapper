<?php

namespace ObjectRelationMapper;


class Connector_PDO implements Connector_Interface
{
	/**
	 * @var PDO
	 */
	protected $db;

	/**
	 * @param null $db
	 * @throws Exception_QueryBuilder
	 */
	public function __construct($db = NULL)
	{
		if(!$db instanceof \PDO){
			throw new Exception_QueryBuilder('Db musi byt instance PDO');
		}

		if(!is_null($db)){
			$this->db = $db;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function query($query, $parameters, $server)
	{
		$query = $this->db->prepare($query);

		foreach ($parameters as $value) {
			$query->bindParam($value[0], $value[1]);
		}

		$query->execute();

		return $query->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * @inheritdoc
	 */
	public function exec($query, $parameters, $server)
	{
		$query = $this->db->prepare($query);

		foreach ($parameters as $value) {
			$query->bindParam($value[0], $value[1]);
		}

		$query->execute();

		return true;
	}
}