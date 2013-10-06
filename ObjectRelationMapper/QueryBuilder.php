<?php

class ObjectRelationMapper_QueryBuilder
{
    protected $queryExecuter;
    protected $orm;

    public function __construct(Interface_ORMQueryExecuter $queryExecuter, ObjectRelationMapper $orm)
    {
        $this->queryExecuter = $queryExecuter;
        $this->orm = $orm;
    }
}