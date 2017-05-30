<?php

namespace ObjectRelationMapper\Tests;

abstract class TestBaseClass extends \ObjectRelationMapper\ORM
{
    public static $__testQueryBuilder = NULL;

    protected function setORMStorages()
    {
        $this->configStorage = 'ObjectRelationMapper\ConfigStorage\Basic';
        if(static::$__testQueryBuilder instanceof \ObjectRelationMapper\QueryBuilder\ABuilder){
            $this->queryBuilder = static::$__testQueryBuilder;
        } else {
            $this->queryBuilder = new \ObjectRelationMapper\QueryBuilder\Yaml(new \ObjectRelationMapper\Connector\Yaml('/tmp/'));
        }
    }

    public function __construct($primaryKey = NULL, \ObjectRelationMapper\QueryBuilder\ABuilder $queryBuilder = NULL)
    {
        if($queryBuilder != NULL){
            static::$__testQueryBuilder = $queryBuilder;
        }

        parent::__construct($primaryKey);
    }
}
