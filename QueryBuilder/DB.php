<?php

namespace ObjectRelationMapper\QueryBuilder;

use ObjectRelationMapper\Connector\ESDB;
use ObjectRelationMapper\Base\AORM;

class DB extends ABuilder
{
	/**
	 * @var ESDB
	 */
	protected $connector;

	public function __construct($connector = NULL)
	{
		if ($connector != NULL) {
			$this->connector = $connector;
		} else {
			$this->connector = new ESDB();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function load(AORM $orm)
	{
		//ted uz vime ze se jedna o select je tedy nutne ho spravne poskladat
		$query = 'SELECT ' . $orm->getAllDbFieldsInternal(', ', true) . ' FROM ' . $orm->getConfigDbTable();

		$columns = Array();
		$params = Array();
		foreach ($orm as $propertyName => $propertyValue) {
			$dbColumn = $orm->getDbField($propertyName);
			$columns[] = $dbColumn . ' <=> :' . $dbColumn;
			$params[] = Array(':' . $dbColumn, $propertyValue);
		}

		if (!empty($columns)) {
			$query .= ' WHERE ' . implode(' AND ', $columns);
		}

		$ordering = $orm->getOrderingOrder();
		if (!empty($ordering)) {
			// ORDER BY col1 ASC, col2 DESC
			$query .= ' ORDER BY ' . $ordering . ' ';
		}

		$query .= ' LIMIT ' . $orm->getOrderingOffset() . ', ' . $orm->getOrderingLimit();

		$query = $this->connector->query($query, $params, $orm->getConfigDbServer());

		if (isset($query[0])) {
			return $query[0];
		} else {
			return Array();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function loadByPrimaryKey(AORM $orm)
	{
		//ted uz vime ze se jedna o select je tedy nutne ho spravne poskladat
		// SELECT columns FROM table WHERE
		$query = 'SELECT ' . $orm->getAllDbFieldsInternal(', ', true) . ' FROM ' . $orm->getConfigDbTable() . ' WHERE ';
		// primaryKey = :primaryKey
		$query .= $orm->getConfigDbPrimaryKey() . ' = :primaryKey ';

		$ordering = $orm->getOrderingOrder();
		if (!empty($ordering)) {
			// ORDER BY col1 ASC, col2 DESC
			$query .= ' ORDER BY ' . $ordering . ' ';
		}
		// LIMIT 0,1
		$query .= ' LIMIT ' . $orm->getOrderingOffset() . ', ' . $orm->getOrderingLimit();

		$params[] = Array(':primaryKey', $orm->primaryKey);

		$query = $this->connector->query($query, $params, $orm->getConfigDbServer());

		if (isset($query[0])) {
			return $query[0];
		} else {
			return Array();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function insert(AORM $orm)
	{
		$query = 'INSERT INTO ' . $orm->getConfigDbTable() . ' SET ';

		$columns = Array();
		$params = Array();
		foreach ($orm as $propertyName => $propertyValue) {
			$dbColumn = $orm->getDbField($propertyName);
			$columns[] = $dbColumn . ' = :' . $dbColumn;
			$params[] = Array(':' . $dbColumn, $propertyValue);
		}

		$query .= implode(', ', $columns);
		if (!empty($columns)) {
			$query = $this->connector->exec($query, $params, $orm->getConfigDbServer());
			$id = $this->connector->queryWrite('SELECT LAST_INSERT_ID() as id', Array(), $orm->getConfigDbServer());
			if($id[0]['id'] != 0){
				$orm->primaryKey = $id[0]['id'];
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function update(AORM $orm, $oldPrimaryKey = NULL)
	{
		$query = 'UPDATE ' . $orm->getConfigDbTable() . ' SET ';

		$columns = Array();
		$params = Array();
		foreach ($orm as $propertyName => $propertyValue) {
			$dbColumn = $orm->getDbField($propertyName);
			if ($oldPrimaryKey != NULL && $orm->primaryKey != $oldPrimaryKey) {
				$columns[] = $dbColumn . ' = :' . $dbColumn;
				$params[] = Array(':' . $dbColumn, $propertyValue);
			} else {
				// save only changed columns
				$columnIsChanged = $propertyName . 'IsChanged';
				if (($dbColumn != $orm->getConfigDbPrimaryKey()) && $orm->$columnIsChanged()) {
					$columns[] = $dbColumn . ' = :' . $dbColumn;
					$params[] = Array(':' . $dbColumn, $propertyValue);
				}
			}
		}

		$query .= implode(', ', $columns);

		$query .= ' WHERE ' . $orm->getConfigDbPrimaryKey() . ' = :primaryKey';

		if ($oldPrimaryKey != NULL && $orm->primaryKey != $oldPrimaryKey) {
			$params[] = Array(':primaryKey', $oldPrimaryKey);
		} else {
			$params[] = Array(':primaryKey', $orm->primaryKey);
		}
		if (!empty($columns)) {
			return $this->connector->exec($query, $params, $orm->getConfigDbServer());
		} else {
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function delete(AORM $orm)
	{
		$query = 'DELETE FROM ' . $orm->getConfigDbTable() . ' ';

		$query .= ' WHERE ' . $orm->getConfigDbPrimaryKey() . ' = :' . $orm->getConfigDbPrimaryKey();
		$params[] = Array(':' . $orm->getConfigDbPrimaryKey(), $orm->primaryKey);
		return $this->connector->exec($query, $params, $orm->getConfigDbServer());
	}

	/**
	 * Vytvoří SQL delete příkaz podle nastavených hodnot ORM
	 * @param AORM $orm
	 * @return bool
	 */
	public function deleteByOrm(AORM $orm)
	{
		$query = 'DELETE FROM ' . $orm->getConfigDbTable() . ' ';

		$columns = Array();
		$params = Array();
		foreach ($orm as $propertyName => $propertyValue) {
			$dbColumn = $orm->getDbField($propertyName);
			$columns[] = $dbColumn . ' <=> :' . $dbColumn;
			$params[] = Array(':' . $dbColumn, $propertyValue);
		}

		if (!empty($columns)) {
			$query .= ' WHERE ' . implode(' AND ', $columns);
		}

		return $this->connector->exec($query, $params, $orm->getConfigDbServer());
	}

	/**
	 * @inheritdoc
	 */
	public function count(AORM $orm)
	{
		//ted uz vime ze se jedna o select je tedy nutne ho spravne poskladat
		$query = 'SELECT count(' . $orm->getConfigDbPrimaryKey() . ') as count FROM ' . $orm->getConfigDbTable();

		$columns = Array();
		$params = Array();
		foreach ($orm as $propertyName => $propertyValue) {
			$dbColumn = $orm->getDbField($propertyName);
			$columns[] = $dbColumn . ' <=> :' . $dbColumn;
			$params[] = Array(':' . $dbColumn, $propertyValue);
		}

		if (!empty($columns)) {
			$query .= ' WHERE ' . implode(' AND ', $columns);
		}

		$query = $this->connector->query($query, $params, $orm->getConfigDbServer());

		if (isset($query[0]['count'])) {
			return $query[0]['count'];
		} else {
			return Array();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function countByQuery(AORM $orm, $query, $params)
	{
		$query = $this->connector->query($query, $params, $orm->getConfigDbServer());

		if (isset($query[0]['count'])) {
			return $query[0]['count'];
		} else {
			return Array();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function loadMultiple(AORM $orm)
	{
        //ted uz vime ze se jedna o select je tedy nutne ho spravne poskladat
		$query = 'SELECT ' . $orm->getAllDbFieldsInternal(', ', true) . ' FROM ' . $orm->getConfigDbTable();

		$columns = Array();
		$params = Array();
		foreach ($orm as $propertyName => $propertyValue) {
			$dbColumn = $orm->getDbField($propertyName);
			$columns[] = $dbColumn . ' <=> :' . $dbColumn;
			$params[] = Array(':' . $dbColumn, $propertyValue);
		}

		if (!empty($columns)) {
			$query .= ' WHERE ' . implode(' AND ', $columns);
		}

		$ordering = $orm->getOrderingOrder();
		if (!empty($ordering)) {
			// ORDER BY col1 ASC, col2 DESC
			$query .= ' ORDER BY ' . $ordering . ' ';
		}

		$query .= ' LIMIT ' . $orm->getOrderingOffset() . ', ' . $orm->getOrderingLimit();
		$query = $this->connector->query($query, $params, $orm->getConfigDbServer());

		if (isset($query)) {
			return $query;
		} else {
			return Array();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function insertMultiple(AORM $orm, Array $data)
	{
		$columns = array_diff($orm->getAllDbFieldsInternal(), Array($orm->getConfigDbPrimaryKey()));
		$query = 'INSERT INTO ' . $orm->getConfigDbTable() . '  ';
		$query .= '(' . implode(',', $columns) . ')';

		$i = 0;
		$values = Array();
		$params = Array();
		foreach ($data as $singleOrm) {
			$cols = Array();
			foreach ($columns as $column) {
				$cols[] = ':' . $i . $column;
				$params[] = Array(':' . $i . $column, $singleOrm->{$orm->getAlias($column)});
			}

			$values[] = '(' . implode(',', $cols) . ')';
			$i++;
		}

		$query .= ' VALUES ' . implode(', ', $values);
		return $this->connector->exec($query, $params, $orm->getConfigDbServer());
	}

    /**
     * @inheritdoc
     */
    public function truncate(AORM $orm)
    {
        $query = 'TRUNCATE ' . $orm->getConfigDbTable() . ';';
        return $this->connector->exec($query, Array(), $orm->getConfigDbServer());
    }

	/**
	 * @inheritdoc
	 */
	public function loadByQuery(AORM $orm, $query, $params, $fetchType = \PDO::FETCH_ASSOC)
	{
		$query = $this->connector->query($query, $params, $orm->getConfigDbServer(), $fetchType);

		if (isset($query)) {
			return $query;
		} else {
			return Array();
		}
	}

    /**
     * @inheritdoc
     */
    public function describe(AORM $orm)
    {
        $query = 'DESCRIBE '.$orm->getConfigDbTable();

        try{
            $query = $this->connector->query($query, Array(), $orm->getConfigDbServer());
        } catch (\PDOException $e){
            if($e->getCode() != '42S02'){
                throw $e;
            }
            $query = Array();
        }

        if (isset($query)) {
            $return = Array();
            foreach($query as $value){
                $return[$value['Field']] = Array(
                    'name' => $value['Field'],
                    'type' => $value['Type'],
                    'primary_key' => (($value['Key'] == 'PRI') ? 1 : 0)
                );
            }
            return $return;
        } else {
            return Array();
        }
    }
}