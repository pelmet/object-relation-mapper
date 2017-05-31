<?php

function autoload($className)
{
    $className = preg_replace('/^ObjectRelationMapper/', '', $className);
    $className = str_replace('\\', '/', $className);
    $className = str_replace('_', '/', $className);

    if (is_file(__DIR__ . '/' . '/' . $className . '.php')) {
        require_once(__DIR__ . '/' . '/' . $className . '.php');
    }
}

spl_autoload_register('autoload');

class Factory
{
    public static function Db()
    {

    }
}

class DB
{
    const FETCH_ASSOC = 3;
}

class Query
{
    public function query()
    {

    }
}

class Exec
{
    public function exec()
    {

    }
}