<?php

namespace ObjectRelationMapper;

class QueryBuilder_DB extends QueryBuilder_Abstract
{
	/**
	 * @var Connector_ESDB
	 */
	protected $connector;

	public function __construct($connector = NULL)
	{
		if(!is_null($connector)){
			$this->connector = $connector;
		} else {
			$this->connector = new Connector_ESDB();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function load(ORM $orm)
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

	/**
	 * @inheritdoc
	 */
	public function loadByPrimaryKey(ORM $orm)
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

		$params[] = Array(':primaryKey', $orm->primaryKey);

		$query = $this->connector->query($query, $params, $orm->getConfigDbServer());

		if(isset($query[0])){
			return $query[0];
		} else {
			return Array();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function insert(ORM $orm)
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
		if(!empty($columns)){
			$query = $this->connector->exec($query, $params, $orm->getConfigDbServer());
			$id = $this->connector->query('SELECT LAST_INSERT_ID() as id', Array(), $orm->getConfigDbServer());
			$orm->primaryKey = $id[0]['id'];
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function update(ORM $orm, $oldPrimaryKey = NULL)
	{
		$query = 'UPDATE ' . $orm->getConfigDbTable() . ' SET ';

		$columns = Array();
		$params = Array();
		foreach($orm as $propertyName => $propertyValue){
			$dbColumn = $orm->getDbField($propertyName);
			if($dbColumn != $orm->getConfigDbPrimaryKey()){
				$columns[] = $dbColumn . ' = :' . $dbColumn;
				$params[] = Array(':' . $dbColumn, $propertyValue);
			}
		}

		$query .= implode(', ', $columns);

		$query .= ' WHERE ' . $orm->getConfigDbPrimaryKey() . ' = :' . $orm->getConfigDbPrimaryKey();
		$params[] = Array(':' . $orm->getConfigDbPrimaryKey(), $orm->primaryKey);

		if(!empty($columns)){
			return $this->connector->exec($query, $params, $orm->getConfigDbServer());
		} else {
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function delete(ORM $orm)
	{
		$query = 'DELETE FROM ' . $orm->getConfigDbTable() . ' ';

		$query .= ' WHERE ' . $orm->getConfigDbPrimaryKey() . ' = :' . $orm->getConfigDbPrimaryKey();
		$params[] = Array(':' . $orm->getConfigDbPrimaryKey(), $orm->primaryKey);
		return $this->connector->exec($query, $params, $orm->getConfigDbServer());
	}

	/**
	 * @inheritdoc
	 */
	public function count(ORM $orm)
	{
		//ted uz vime ze se jedna o select je tedy nutne ho spravne poskladat
		$query = 'SELECT count(' . $orm->getConfigDbPrimaryKey() . ') as count FROM ' . $orm->getConfigDbTable();

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

		$query = $this->connector->query($query, $params, $orm->getConfigDbServer());

		if(isset($query[0]['count'])){
			return $query[0]['count'];
		} else {
			return Array();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function countByQuery(ORM $orm, $query, $params)
	{
		$query = $this->connector->query($query, $params, $orm->getConfigDbServer());

		if(isset($query[0]['count'])){
			return $query[0]['count'];
		} else {
			return Array();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function loadMultiple(ORM $orm)
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

		if(isset($query)){
			return $query;
		} else {
			return Array();
		}
	}

    public function loadByQuery(ORM $orm, $query, $params)
    {
        $query = $this->connector->query($query, $params, $orm->getConfigDbServer());

        if(isset($query)){
            return $query;
        } else {
            return Array();
        }
    }
}