<?php

class CommonTestClass extends \PHPUnit\Framework\TestCase
{
    public $connections = Array();
    public $queryBuilders = Array();

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->queryBuilders['mysql'] = new \ObjectRelationMapper\QueryBuilder\DB(new \ObjectRelationMapper\Connector\PDO($this->getConnection('mysql')));
        $this->queryBuilders['sqlite'] = new \ObjectRelationMapper\QueryBuilder\SQLite(new \ObjectRelationMapper\Connector\PDO($this->getConnection('sqlite')));
        $this->queryBuilders['yml'] = new \ObjectRelationMapper\QueryBuilder\Yaml(new \ObjectRelationMapper\Connector\Yaml('/tmp/'));
    }

    /**
     * @param $type
     * @return \PDO
     */
    public function getConnection($type)
    {
        if(!isset($this->connections[$type])){
            $this->connections[$type] = new \PDO(
                $GLOBALS['databases'][$type]['dsn'],
                $GLOBALS['databases'][$type]['user'],
                $GLOBALS['databases'][$type]['pass'],
                Array(\PDO::ATTR_PERSISTENT => true, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION)
            );
        }

        return $this->connections[$type];
    }

    public function getQueryBuilder($type)
    {
        return $this->queryBuilders[$type];
    }

    public function setUp()
    {
        foreach($this->connections as $type => $database){
            /**
             * @var $database \PDO
             */
            $database->exec(file_get_contents(BASE_DIR . '/Databases/'.$type.'/base.sql'));
            if(file_exists(BASE_DIR . '/Databases/'.$type.'/'.get_called_class().'.sql')){
                $database->exec(file_get_contents(BASE_DIR . '/Databases/'.$type.'/'.get_called_class().'.sql'));
            }
        }
    }

    public function providerBasic()
	{
		return Array(
			'mysql' => Array('mysql', new \ObjectRelationMapper\Tests\ORMTest(NULL, $this->getQueryBuilder('mysql'))),
            'sqlite' => Array('sqlite', new \ObjectRelationMapper\Tests\ORMTest(NULL, $this->getQueryBuilder('sqlite'))),
            //'yml' => Array('yml', new \ObjectRelationMapper\Tests\ORMTest(NULL, $this->getQueryBuilder('yml'))),
		);
    }

    public function providerValidation()
    {
        return Array(
            'mysql' => Array('mysql', new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('mysql'))),
            'sqlite' => Array('sqlite', new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('sqlite'))),
            'yml' => Array('yml', new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('yml'))),
        );
    }

    public function providerSearch()
    {
        return Array(
            'mysql' => Array('mysql', new \ObjectRelationMapper\Tests\ORMTest(NULL, $this->getQueryBuilder('mysql'))),
            'sqlite' => Array('sqlite', new \ObjectRelationMapper\Tests\ORMTest(NULL, $this->getQueryBuilder('sqlite'))),
            //'yml' => Array('yml', new \ObjectRelationMapper\Tests\ORMTest(NULL, $this->getQueryBuilder('yml'))),
        );
    }
}