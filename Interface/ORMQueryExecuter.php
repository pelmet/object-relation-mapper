<?php

interface Interface_ORMQueryExecuter
{
    public function __construct(ObjectRelationMapper $orm);
    public function executeLoad();
    public function executeCreate();
    public function executeUpdate();
    public function executeDelete();
}