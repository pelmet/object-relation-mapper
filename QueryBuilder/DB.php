<?php

class ObjectRelationMapper_QueryBuilder_DB extends ObjectRelationMapper_QueryBuilder_Abstract
{
	/**
	 * @var ObjectRelationMapper_Connector_ESDB
	 */
	protected $connector;

	public function __construct($connector = NULL)
	{
		if(!is_null($connector)){
			$this->connector = $connector;
		} else {
			$this->connector = new ObjectRelationMapper_Connector_ESDB();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function load(ObjectRelationMapper_ORM $orm)
	{
		//ted uz vime ze se jedna o select je tedy nutne ho spravne poskladat
		$query = 'SELECT ' . $orm->getAllDbFields(', ', true) . ' FROM ' . $orm->getConfigDbTable();

		$columns = Array();
		$params = Array();
		foreach($orm as $propertyName => $propertyValue){
			$dbColumn = $orm->getDbField($propertyName);
			$columns[] = $dbColumn . ' = :' . $dbColumn;
			$params[] = Array(':' . $dbColumn, $propertyValue);
		}

		if(!empty($columns)){
			$query .= ' WHERE ' . implode(' AND ', $columns);
		}

		$ordering = $orm->getOrderingOrder();
		if(!empty($ordering)){
			// ORDER BY col1 ASC, col2 DESC
			$query .= ' ORDER BY ' . $ordering . ' ';
		}

		$query .= ' LIMIT ' . $orm->getOrderingOffset() . ', ' . $orm->getOrderingLimit();

		$query = $this->connector->query($query, $params, $orm->getConfigDbServer());

		if(isset($query[0])){
			return $query[0];
		} else {
			return Array();
		}
	}

	public function loadByPrimaryKey(ObjectRelationMapper_ORM $orm)
	{
		//ted uz vime ze se jedna o select je tedy nutne ho spravne poskladat
		// SELECT columns FROM table WHERE
		$query  = 'SELECT ' . $orm->getAllDbFields(', ', true) . ' FROM ' . $orm->getConfigDbTable() . ' WHERE ';
		// primaryKey = :primaryKey
		$query .= $orm->getConfigDbPrimaryKey() . ' = :primaryKey ';

		$ordering = $orm->getOrderingOrder();
		if(!empty($ordering)){
			// ORDER BY col1 ASC, col2 DESC
			$query .= ' ORDER BY ' . $ordering . ' ';
		}
		// LIMIT 0,1
		$query .= ' LIMIT ' . $orm->getOrderingOffset() . ', ' . $orm->getOrderingLimit();

		$params[] = Array(':primaryKey', $orm->getPrimaryKey());

		$query = $this->connector->query($query, $params, $orm->getConfigDbServer());

		if(isset($query[0])){
			return $query[0];
		} else {
			return Array();
		}
	}

	public function insert(ObjectRelationMapper_ORM $orm)
	{
		$query = 'INSERT INTO ' . $orm->getConfigDbTable() . ' SET ';

		$columns = Array();
		$params = Array();
		foreach($orm as $propertyName => $propertyValue){
			$dbColumn = $orm->getDbField($propertyName);
			$columns[] = $dbColumn . ' = :' . $dbColumn;
			$params[] = Array(':' . $dbColumn, $propertyValue);
		}

		$query .= implode(', ', $columns);


	}

	public function update(ObjectRelationMapper_ORM $orm)
	{
		// TODO: Implement update() method.
	}

	public function delete(ObjectRelationMapper_ORM $orm)
	{
		// rozdilne casti pro vsechny typy query
		if($queryType == self::INSERT){
			$query .= 'INSERT INTO ';
		} elseif($queryType == self::UPDATE){
			$query .= 'UPDATE ';
		} elseif($queryType == self::DELETE){
			$query .= 'DELETE FROM ';
		}

		// spolecnou casti je jmeno tabulky
		$query .= ' '. $queryData[self::DEFAULT_CONFIG_ARRAY_NAME_TABLE] . ' ';

		// update a insert nyni potrebuji vycet parametru, na to budeme mit pomocnou fci, ktera vrati pole
		if($queryType == self::INSERT || $queryType == self::UPDATE){
			$query .= ' SET ';

			//sestaveni query parametru
			if($queryType == self::INSERT){
				if($insertPrimaryKey == false){
					$queryParams = $this->queryArgumentsBuilder($queryData[self::DEFAULT_CONFIG_ARRAY_NAME_PRIMARY_KEY]);
				} else {
					$queryParams = $this->queryArgumentsBuilder();
				}

			} else {
				$queryParams = $this->queryArgumentsBuilder($queryData[self::DEFAULT_CONFIG_ARRAY_NAME_PRIMARY_KEY]); // chceme vsechny args krome primarniho klice, s tim si hrajem jinak
			}

			$queryPart = Array();

			if($queryParams == false){ // problem a konec
				return false;
			}

			foreach($queryParams['arguments'] as $value){ // sestaveni parametru jako argumentoveho pole pro query
				$queryPart[] = $value;
			}

			foreach($queryParams['params'] as $value){ // sestaveni parametroveho pole
				$params[] = $value;
			}

			$query .= implode(', ', $queryPart);
		}

		if($queryType == self::DELETE || $queryType == self::UPDATE){
			$query .= ' WHERE '. $queryData[self::DEFAULT_CONFIG_ARRAY_NAME_PRIMARY_KEY] . ' = :'.$queryData[self::DEFAULT_CONFIG_ARRAY_NAME_PRIMARY_KEY];
			$params[] = Array(':'.$queryData[self::DEFAULT_CONFIG_ARRAY_NAME_PRIMARY_KEY], $this->{$this->getAlias($queryData[self::DEFAULT_CONFIG_ARRAY_NAME_PRIMARY_KEY])});
		}
	}

	public function count(ObjectRelationMapper_ORM $orm)
	{
		// TODO: Implement count() method.
	}
}