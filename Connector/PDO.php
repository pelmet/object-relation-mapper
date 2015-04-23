<?php

namespace ObjectRelationMapper\Connector;


use ObjectRelationMapper\Exception\QueryBuilder as EQueryBuilder;

class PDO implements IConnector
{
	/**
	 * @var PDO
	 */
	protected $db;

	/**
	 * @param null $db
	 * @throws EQueryBuilder
	 */
	public function __construct($db = NULL)
	{
		if (!$db instanceof \PDO) {
			throw new EQueryBuilder('Db musi byt instance PDO');
		}

		if ($db != NULL) {
			$this->db = $db;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function query($query, $parameters, $server, $fetchType = \PDO::FETCH_ASSOC)
	{
		$query = $this->db->prepare($query);

		foreach ($parameters as $value) {
			$query->bindParam($value[0], $value[1]);
		}

		$query->execute();

		return $query->fetchAll($fetchType);
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