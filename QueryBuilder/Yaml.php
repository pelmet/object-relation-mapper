<?php

namespace ObjectRelationMapper\QueryBuilder;

use ObjectRelationMapper\Connector\ESDB;
use ObjectRelationMapper\Base\AORM;
use ObjectRelationMapper\Exception\QueryBuilder;

class Yaml extends ABuilder
{
    /**
     * @var array
     */
    protected $connectors = Array();

    public function __construct(\ObjectRelationMapper\Connector\Yaml $connector)
    {
        $this->addConnector($connector);
    }

    public function addConnector(\ObjectRelationMapper\Connector\Yaml $connector)
    {
        $this->connectors[$connector->connectionAlias] = $connector;
    }

    protected function getFilename(AORM $orm)
    {
        return $this->connectors[$orm->getConfigDbServer()]->storagePath.$orm->getConfigDbTable().$this->connectors[$orm->getConfigDbServer()]->fileExtension;
    }

    protected function checkFile(AORM $orm)
    {
        if(!is_file($this->getFilename($orm))){
            $file = fopen($this->getFilename($orm), "w");
            fclose($file);
            file_put_contents($this->getFilename($orm), "---\nvalues:\n...");
        }

        return is_file($this->getFilename($orm));
    }

    /**
     * @inheritdoc
     */
    public function load(AORM $orm)
    {
        if(!$this->checkFile($orm)){
            throw new QueryBuilder('Cant open the file for writing');
        }

        $fileData = yaml_parse_file($this->getFilename($orm));

        if(!empty($fileData['values'])){
            $keys = Array();
            foreach ($orm as $propertyName => $propertyValue) {
                $keys[$propertyName] = array_search($propertyValue, array_column($fileData['values'], $orm->getDbField($propertyName)));
            }

            $key = false;
            if(in_array(false, $keys, true)){
                $key = false;
            } else {
                foreach(array_count_values($keys) as $keyIndex => $count){
                    if($count == count($keys)){
                        $key = $keyIndex;
                        break;
                    }
                }
            }
        } else {
            $key = false;
        }

        if($key === false){
            return Array();
        } else {
            return $fileData['values'][$key];
        }
    }

    /**
     * @inheritdoc
     */
    public function loadByPrimaryKey(AORM $orm)
    {
        if(!$this->checkFile($orm)){
            throw new QueryBuilder('Cant open the file for writing');
        }
        $fileData = yaml_parse_file($this->getFilename($orm));

        if(!empty($fileData['values'])){
            $key = array_search($orm->primaryKey, array_column($fileData['values'], $orm->getConfigDbPrimaryKey()));
        } else {
            $key = false;
        }

        if($key === false){
            return Array();
        } else {
            return $fileData['values'][$key];
        }
    }

    /**
     * @inheritdoc
     */
    public function insert(AORM $orm)
    {
        if(!$this->checkFile($orm)){
            throw new QueryBuilder('Cant open the file for writing');
        }

        $fileData = yaml_parse_file($this->getFilename($orm));

        $add = Array();
        foreach ($orm as $propertyName => $propertyValue) {
            $dbColumn = $orm->getDbField($propertyName);
            $add[$dbColumn] = $propertyValue;
        }

        $add[$orm->getConfigDbPrimaryKey()] = count($fileData['values']) + 1;

        foreach(array_diff_key(array_flip($orm->getAllDbFieldsInternal()), $add) as $key => $value){
            $add[$key] = NULL;
        }

        $fileData['values'][] = $add;
        yaml_emit_file($this->getFilename($orm), $fileData);
    }

    /**
     * @inheritdoc
     */
    public function update(AORM $orm, $oldPrimaryKey = NULL)
    {
        if(!$this->checkFile($orm)){
            throw new QueryBuilder('Cant open the file for writing');
        }

        $fileData = yaml_parse_file($this->getFilename($orm));
        $primaryKey = array_search($orm->primaryKey, array_column($fileData['values'], $orm->getConfigDbPrimaryKey()));

        $add = Array();
        foreach ($orm as $propertyName => $propertyValue) {
            $dbColumn = $orm->getDbField($propertyName);
            $add[$dbColumn] = $propertyValue;
        }

        foreach(array_diff_key(array_flip($orm->getAllDbFieldsInternal()), $add) as $key => $value){
            $add[$key] = NULL;
        }

        $fileData['values'][$primaryKey] = $add;
        yaml_emit_file($this->getFilename($orm), $fileData);
    }

    /**
     * @inheritdoc
     */
    public function delete(AORM $orm)
    {
        if(!$this->checkFile($orm)){
            throw new QueryBuilder('Cant open the file for writing');
        }

        $fileData = yaml_parse_file($this->getFilename($orm));

        if(!empty($fileData['values'])){
            $primaryKey = array_search($orm->primaryKey, array_column($fileData['values'], $orm->getConfigDbPrimaryKey()));

            if(isset($fileData['values'][$primaryKey])){
                unset($fileData['values'][$primaryKey]);
            }
        }

        yaml_emit_file($this->getFilename($orm), $fileData);
    }

    /**
     * @inheritdoc
     */
    public function deleteByOrm(AORM $orm)
    {
        throw new \ObjectRelationMapper\Exception\QueryBuilder("Cant be implemented");
    }

    /**
     * @inheritdoc
     */
    public function count(AORM $orm)
    {
        if(!$this->checkFile($orm)){
            throw new QueryBuilder('Cant open the file for writing');
        }

        $fileData = yaml_parse_file($this->getFilename($orm));
        return count($fileData['values']);
    }

    /**
     * @inheritdoc
     */
    public function countByQuery(AORM $orm, $query, $params)
    {
        throw new \ObjectRelationMapper\Exception\QueryBuilder("Cant be implemented");
    }

    /**
     * @inheritdoc
     */
    public function loadMultiple(AORM $orm)
    {
        if(!$this->checkFile($orm)){
            throw new QueryBuilder('Cant open the file for writing');
        }

        $fileData = yaml_parse_file($this->getFilename($orm));

        $keys = Array();
        foreach ($orm as $propertyName => $propertyValue) {
            $keys[$propertyName] = array_keys(array_column($fileData['values'], $orm->getDbField($propertyName), $orm->getConfigDbPrimaryKey()), $propertyValue);
        }

        if (empty($keys)){
            return $fileData['values'];
        } elseif (count($keys) == 1) {
            foreach($keys as $search){
                $return = Array();
                foreach($search as $primaryKey){
                    $return[] = $fileData['values'][array_search($primaryKey, array_column($fileData['values'], $orm->getConfigDbPrimaryKey()))];
                }
                return $return;
            }
        } else {
            $return =  Array();
            foreach(call_user_func_array('array_intersect', $keys) as $primaryKey){
                $return[] = $fileData['values'][array_search($primaryKey, array_column($fileData['values'], $orm->getConfigDbPrimaryKey()))];
            }
            return $return;
        }
    }

    /**
     * @inheritdoc
     */
    public function insertMultiple(AORM $orm, Array $data)
    {
        throw new \ObjectRelationMapper\Exception\QueryBuilder("Cant be implemented");
    }

    /**
     * @inheritdoc
     */
    public function truncate(AORM $orm)
    {
        unlink($this->getFilename($orm));
        $this->checkFile($orm);
    }

    /**
     * @inheritdoc
     */
    public function loadByQuery(AORM $orm, $query, $params, $fetchType = \PDO::FETCH_ASSOC)
    {
        throw new \ObjectRelationMapper\Exception\QueryBuilder("Cant be implemented");
    }

    public function describe(AORM $orm)
    {
        throw new \ObjectRelationMapper\Exception\QueryBuilder("Cant be implemented");
    }
}