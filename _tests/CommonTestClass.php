<?php

class CommonTestClass extends \PHPUnit\Framework\TestCase
{
    public $connections = Array();
    public $queryBuilders = Array();
    protected $excludedFiles = Array(
        '.',
        '..',
        '.gitignore'
    );

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        foreach($GLOBALS['databases'] as $databaseName => $database){
            if($database['test_data'] == 'mysql'){
                $this->queryBuilders[$databaseName] = new \ObjectRelationMapper\QueryBuilder\DB(new \ObjectRelationMapper\Connector\PDO($this->getConnection($databaseName)));
            } elseif($database['test_data'] == 'sqlite'){
                $this->queryBuilders[$databaseName] = new \ObjectRelationMapper\QueryBuilder\SQLite(new \ObjectRelationMapper\Connector\PDO($this->getConnection($databaseName)));
            } elseif($database['test_data'] == 'yml'){
                $this->queryBuilders[$databaseName] = new \ObjectRelationMapper\QueryBuilder\Yaml(new \ObjectRelationMapper\Connector\Yaml('/tmp/'));
            } else {
                throw new \Exception('Unknown databse format');
            }
        }
    }

    public function providerBasic()
    {
        $orms = Array();

        foreach($this->queryBuilders as $builderType => $builder){
            $orms[$builderType] = Array($builderType, new \ObjectRelationMapper\Tests\ORMTest(NULL, $builder));
        }

        return $orms;
    }

    public function providerValidation()
    {
        $orms = Array();

        foreach($this->queryBuilders as $builderType => $builder){
            $orms[$builderType] = Array($builderType, new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $builder));
        }

        return $orms;
    }

    public function providerSearch()
    {
        $orms = Array();

        foreach($this->queryBuilders as $builderType => $builder){
            $orms[$builderType] = Array($builderType, new \ObjectRelationMapper\Tests\ORMTest(NULL, $builder));
        }

        return array_diff_key($orms, Array('yml' => 0));
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

    /**
     * @param $connector
     * @return bool
     */
    protected function isFileConnector($connector)
    {
        return $GLOBALS['databases'][$connector]['type'] == 'file';
    }

    /**
     * @param $type
     * @param $file
     * @return string
     * @throws Exception
     */
    protected function fileConnectorGetFileData($type, $file)
    {
        switch($type){
            case 'yml':
                $result = yaml_parse_file($file);
                return $result['values'];
                break;
            default:
                throw new \Exception('No such connector configured: '.$type);
            }
    }

    /**
     * @param $connector
     * return array
     */
    protected function getConnectorConfiguration($connector)
    {
        return $GLOBALS['databases'][$connector];
    }

    public function setUp()
    {
        foreach($this->connections as $type => $database){
            /**
             * @var $database \PDO
             */
            $database->exec(file_get_contents(BASE_DIR . '/Databases/'.$this->getConnectorConfiguration($type)['test_data'].'/base.sql'));
            if(file_exists(BASE_DIR . '/Databases/'.$this->getConnectorConfiguration($type)['test_data'].'/'.get_called_class().'.sql')){
                $database->exec(file_get_contents(BASE_DIR . '/Databases/'.$this->getConnectorConfiguration($type)['test_data'].'/'.get_called_class().'.sql'));
            }
        }

        foreach(array_diff_key($this->queryBuilders, $this->connections) as $type => $connector){
            $destination = $this->getConnectorConfiguration($type)['dsn'];
            // Upload base files into their location
            $source = BASE_DIR . '/Databases/'.$this->getConnectorConfiguration($type)['test_data'].'/base';
            if(is_dir($source)){
                foreach(array_diff(scandir($source), $this->excludedFiles) as $file){
                    file_put_contents($destination . $file, file_get_contents($source . '/' . $file));
                }
            }

            $source = BASE_DIR . '/Databases/'.$this->getConnectorConfiguration($type)['test_data'].'/'.get_called_class();
            // Upload specific test files (if any) into their location
            if(is_dir($source)){
                foreach(array_diff(scandir($source), $this->excludedFiles) as $file){
                    file_put_contents($destination . $file, file_get_contents($source . '/' . $file));
                }
            }
        }
    }
}