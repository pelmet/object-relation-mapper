<?php

namespace ObjectRelationMapper\Search\Connector;

use ObjectRelationMapper\ORM;
use ObjectRelationMapper\Search\ResultProcess;

abstract class AConnector
{
    /**
     * @var ORM
     */
    protected $orm;

    protected $aliases = Array();

    protected $search = Array();
    protected $params = Array();
    protected $joinTables = Array();
    protected $selectCols = Array();
    protected $searchCount = 0;
    protected $query;
    protected $countQuery;
    protected $limit = 999999;
    protected $offset = 0;
    protected $imploder = ' AND ';
    protected $ordering = Array();
    protected $results = Array();
    protected $group = Array();
    protected $functionColumn = Array();
    protected $additionalOrms = Array();

    abstract public function composeLoadQuery();
    abstract public function composeCountQuery();

    public function __construct(ORM $orm)
    {
        $this->orm = $orm;
        $this->aliases = $orm->getAllAliases();
        $this->selectCols[$orm->getConfigDbTable()] = $orm->getAllDbFields(NULL, true);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Prida childa, kdyz nechceme vyhledavat podle parametru
     * @param string $childName
     * @param string $joinType
     * @param array $additionalCols
     * @param string $matching
     * @return \ObjectRelationMapper\Base\AORM
     */
    protected function addChild($childName, $joinType = 'LEFT', $additionalCols = Array(), $matching = '=')
    {
        if (!isset($this->additionalOrms[$childName])) {
            $child = $this->orm->{'getChild' . ucfirst($childName) . 'Config'}();
            $orm = new $child->ormName();
            $this->additionalOrms[$childName] = $orm;

            $join = ' ' . $joinType . ' JOIN ' . $orm->getConfigDbTable() . ' ON
					' . $this->orm->getConfigDbTable() . '.' . $child->localKey . ' = ' . $orm->getConfigDbTable() . '.' . $child->foreignKey . ' ';

            foreach ($additionalCols as $col => $value) {
                $join .= ' AND ' . $orm->getDbField($col, true) . ' ' . $matching . ' ' . $this->addParameter($value) . ' ';
            }

            $this->joinTables[$childName] = $join;
            $this->selectCols[$childName] = $orm->getAllDbFields(NULL, true);
        }

        return new $this->additionalOrms[$childName];
    }

    /**
     * Prida DbField
     * @param string $field
     * @return string
     */
    protected function dbFieldName($field)
    {
        if (preg_match('/(.*)\.(.*)/', $field, $matches)) {
            return $this->getOrmDbColumn($this->addChild($matches[1]), $matches[2]);
        } else {
            $this->aliasExists($field);
            return $this->getOrmDbColumn($this->orm, $field);
        }
    }

    /**
     * Vrati column z childa
     * @param \ObjectRelationMapper\Base\AORM $orm
     * @param string $alias
     * @return string
     */
    protected function getOrmDbColumn(\ObjectRelationMapper\Base\AORM $orm, $alias)
    {
        return $orm->getDbField($alias, true);
    }

    /**
     * Vrati, zda na ORM existuje Alias
     * @param string $property
     * @throws \ObjectRelationMapper\Exception\ORM
     */
    protected function aliasExists($property)
    {
        if (!in_array($property, $this->aliases)) {
            throw new \ObjectRelationMapper\Exception\ORM('Alias ' . $property . ' neexistuje na ORM ' . $this->orm->getConfigObject());
        }
    }

    /**
     * pripravuje hodnoty pro PDO a vraci prepared nazvy
     * @param array $values
     * @return array
     */
    protected function prepareInValues(Array $values)
    {
        $preparedValues = array();
        foreach($values AS $value){
            $preparedValues[] = $this->addParameter($value);
        }
        return $preparedValues;
    }

    /**
     * Prida Parametr
     * @param string $value
     * @return string
     */
    protected function addParameter($value)
    {
        $this->searchCount++;
        $this->params[] = Array(':param' . $this->searchCount, $value);
        return ':param' . $this->searchCount;
    }



    protected function getSelectCols()
    {
        return implode(', ', $this->getSelectColsInArray());
    }

    protected function getSelectColsInArray(){
        $return = Array();

        foreach ($this->selectCols as $cols) {
            foreach ($cols as &$col) {
                if (isset($this->functionColumn[$col])) {
                    $col = $this->functionColumn[$col];
                }
            }
            $return = array_merge($return, $cols);
        }

        return $return;
    }

    /**
     * if used \PDO::FETCH_NUM method rename columns from numbers to format table.column
     * @param array $results
     * @return mixed
     */
    protected function renameFieldsFromFetchNum($results)
    {
        $cols = $this->getSelectColsInArray();
        $i = $j = 0;
        $return = array();
        foreach($results AS $result){
            $j = 0;
            foreach($result As $row){
                $return[$i][$cols[$j]] = $row;
                $j++;
            }
            $i++;
        }
        return $return;
    }

    /**
     * @return array
     */
    public function getResultsWithChildrenLoaded($rows = Array())
    {
        if(empty($rows)){
            $rows = $this->getQueryBuilderResults();
        }

        $primaryOrmAliases = new ResultProcess($this->orm);
        $results = $additionalOrmsAliases = array();

        foreach ($this->additionalOrms as $key => $additionalOrm) {
            $additionalOrmsAliases[$key] = new ResultProcess($additionalOrm);
        }

        $primaryKeyIndex = array_search($primaryOrmAliases->orm->getConfigDbPrimaryKey(), $primaryOrmAliases->dbFields);

        foreach($rows as $row){
            $processedRow = $row;
            $primaryValue = $row[$primaryKeyIndex];
            $primarySliced = array_splice($processedRow, 0, $primaryOrmAliases->size);
            if (isset($results[$primaryValue])) {
                $primaryOrm = $results[$primaryValue];
            } else {
                $primaryOrm = new $primaryOrmAliases->orm;
                $tempPrimaryValues = array_combine($primaryOrmAliases->dbFields ,$primarySliced);
                $primaryOrm->load($tempPrimaryValues);
            }

            foreach ($additionalOrmsAliases as $childName => $additionalOrmAliases) {
                /** @var ResultProcess $additionalOrmAliases */
                $children = $primaryOrm->$childName;

                $tempValue = array_combine($additionalOrmAliases->dbFields ,array_splice($processedRow, 0, $additionalOrmAliases->size));
                $orm = new $additionalOrmAliases->orm;
                $orm->load($tempValue);
                $children[] = $orm;
                $primaryOrm->$childName = $children;
            }
            $results[$primaryValue] = $primaryOrm;
        }

        return array_values($results);
    }

    public function getResultsInArray()
    {
        return $this->renameFieldsFromFetchNum($this->getQueryBuilderResults());
    }

    protected function getQueryBuilderResults()
    {
        $fetchType = \PDO::FETCH_NUM;
        $queryBuilder = $this->orm->getQueryBuilder();
        return $queryBuilder->loadByQuery($this->orm, $this->composeLoadQuery(), $this->params, $fetchType);
    }

    public function runCustomLoadQuery($query, Array $params, $fetchType = \PDO::FETCH_ASSOC)
    {
        return $this->orm->getQueryBuilder()->loadByQuery($this->orm, $query, $params, $fetchType);
    }

    public function runCustomExecQuery($query, Array $params)
    {
        return $this->orm->getQueryBuilder()->execByQuery($this->orm, $query, $params);
    }
}
