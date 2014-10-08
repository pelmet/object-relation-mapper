<?php

namespace ObjectRelationMapper\QueryBuilder;

use ObjectRelationMapper\Connector\ESDB;
use ObjectRelationMapper\Base\AORM;

class DBUpdateFromChangedProperties extends Db
{
	/**
	 * @inheritdoc
	 */
	public function update(AORM $orm, $oldPrimaryKey = NULL)
	{
		$query = 'UPDATE ' . $orm->getConfigDbTable() . ' SET ';

		$columns = Array();
		$params = Array();

		foreach($orm->getChangedVariables() AS $propertyName){
			$propertyValue = $orm->getValue($propertyName);
			$dbColumn = $orm->getDbField($propertyName);
			if ($oldPrimaryKey != NULL && $orm->primaryKey != $oldPrimaryKey) {
				$columns[] = $dbColumn . ' = :' . $dbColumn;
				$params[] = Array(':' . $dbColumn, $propertyValue);
			} elseif ($dbColumn != $orm->getConfigDbPrimaryKey()){
				$propertyValue = $orm->getSenitazedValue($propertyName);
				$columns[] = $dbColumn . ' = :' . $dbColumn;
				$params[] = Array(':' . $dbColumn, $propertyValue);
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
}