<?php

namespace ObjectRelationMapper\QueryBuilder;

use ObjectRelationMapper\Connector\ESDB;
use ObjectRelationMapper\Base\AORM;

class SQLite extends DB
{
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
            $columns[] = $dbColumn . ' = :' . $dbColumn;
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
            $columns[] = $dbColumn . ' = :' . $dbColumn;
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
            $columns[] = $dbColumn . ' = :' . $dbColumn;
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
    public function loadMultiple(AORM $orm)
    {
        //ted uz vime ze se jedna o select je tedy nutne ho spravne poskladat
        $query = 'SELECT ' . $orm->getAllDbFieldsInternal(', ', true) . ' FROM ' . $orm->getConfigDbTable();

        $columns = Array();
        $params = Array();
        foreach ($orm as $propertyName => $propertyValue) {
            $dbColumn = $orm->getDbField($propertyName);
            $columns[] = $dbColumn . ' = :' . $dbColumn;
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
    public function insert(AORM $orm)
    {
        $query = 'INSERT INTO ' . $orm->getConfigDbTable() . ' ';

        $columns = Array();
        $params = Array();
        $values = Array();
        foreach ($orm as $propertyName => $propertyValue) {
            $dbColumn = $orm->getDbField($propertyName);
            $columns[] = $dbColumn;
            $values[] = ':' . $dbColumn;
            $params[] = Array(':' . $dbColumn, $propertyValue);
        }

        $query .= '( ' . implode(', ', $columns) . ' ) VALUES ';
        $query .= '( ' . implode(', ', $values) . ' ) ';
        if (!empty($columns)) {
            $query = $this->connector->exec($query, $params, $orm->getConfigDbServer());
            $id = $this->connector->query('SELECT last_insert_rowid() as id', Array(), $orm->getConfigDbServer());
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
    public function describe(AORM $orm)
    {
        $query = 'PRAGMA table_info('.$orm->getConfigDbTable().')';

        $query = $this->connector->query($query, Array(), $orm->getConfigDbServer());

        if (isset($query)) {
            $return = Array();
            foreach($query as $value){
                $return[$value['name']] = Array(
                    'name' => $value['name'],
                    'type' => $value['type'],
                    'primary_key' => $value['pk']
                );
            }
            return $return;
        } else {
            return Array();
        }
    }
}