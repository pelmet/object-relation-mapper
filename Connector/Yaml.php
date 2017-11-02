<?php

namespace ObjectRelationMapper\Connector;

class Yaml implements IConnector
{
    public $fileExtension;
    public $storagePath;
    public $connectionAlias;

    public function __construct($storagePath, $fileExtension = '.yml', $connectionAlias = 'master')
    {
        $this->fileExtension = $fileExtension;
        $this->storagePath = $storagePath;
        $this->connectionAlias = $connectionAlias;
    }

    /**
     * @inheritdoc
     */
    public function query($query, $parameters, $server, $fetchType = \PDO::FETCH_ASSOC)
    {
        throw new \Exception('Cant be implemented');
    }

    /**
     * @inheritdoc
     */
    public function queryWrite($query, $parameters, $server, $fetchType = \PDO::FETCH_ASSOC)
    {
        throw new \Exception('Cant be implemented');
    }

    /**
     * @inheritdoc
     */
    public function exec($query, $parameters, $server)
    {
        throw new \Exception('Cant be implemented');
    }
}