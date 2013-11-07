<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'ormtestuser');
define('DB_PASS', 'testpass');
define('DB_DB'  , 'orm_test_db');

$abstract = Array();
$findFiles = exec('(cd .. && find ./ -path ./_tests -prune -o -type f -name \*.php | grep -v _tests | grep Abstract)', $abstract);

foreach($abstract as $includeFile){
	require_once(realpath(__DIR__ . '/../' . substr($includeFile, 1)));
}

$interface = Array();
$findFiles = exec('(cd .. && find ./ -path ./_tests -prune -o -type f -name \*.php | grep -v _tests | grep Interface)', $interface);

foreach($interface as $includeFile){
	require_once(realpath(__DIR__ . '/../' . substr($includeFile, 1)));
}

$return = Array();
$findFiles = exec('(cd .. && find ./ -path ./_tests -prune -o -type f -name \*.php | grep -v _tests)', $return);

foreach($return as $includeFile){
	require_once(realpath(__DIR__ . '/../' . substr($includeFile, 1)));
}

/**
 * Class ORMTest
 * @property int id
 * @property string startTime
 * @property string endTime
 * @property int status
 * @property string command
 */
class ORMTest extends ObjectRelationMapper_ORM
{
	protected function setORMStorages()
	{
		$this->configStorage 	= 'ObjectRelationMapper_ConfigStorage_Basic';

		$connector = new ObjectRelationMapper_Connector_PDO(new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB , DB_USER, DB_PASS, Array(PDO::ATTR_PERSISTENT => true)));
		$this->queryBuilder = new ObjectRelationMapper_QueryBuilder_DB($connector);
	}

	function setUp()
	{
		$this->addColumn('qc_id', 'id', 'int', '10');
		$this->addColumn('qc_time_start', 'startTime', 'int', '12');
		$this->addColumn('qc_time_end', 'endTime', 'int', '12');
		$this->addColumn('qc_status', 'status', 'int', '1');
		$this->addColumn('qc_command', 'command', 'string', '2000');

		$this->addChild('ORMTestChild', 'logs', 'qc_id', 'qc_id');

		$this->setConfigDbPrimaryKey	('qc_id');
		$this->setConfigDbServer		('master');
		$this->setConfigObject			(__CLASS__);
		$this->setConfigDbTable			('d_queued_commands');
	}
}

/**
 * Class ORMTestChild
 * @property int id
 * @property string startTime
 * @property string endTime
 * @property int status
 * @property string command
 */
class ORMTestChild extends ObjectRelationMapper_ORM
{
	protected function setORMStorages()
	{
		$this->configStorage 	= 'ObjectRelationMapper_ConfigStorage_Basic';

		$connector = new ObjectRelationMapper_Connector_PDO(new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB , DB_USER, DB_PASS, Array(PDO::ATTR_PERSISTENT => true)));
		$this->queryBuilder = new ObjectRelationMapper_QueryBuilder_DB($connector);
	}

	function setUp()
	{
		$this->addColumn('qcl_id', 'id', 'int', '10');
		$this->addColumn('qc_id', 'queuedCommandId', 'int', '12');
		$this->addColumn('qcl_text', 'text', 'string', '2000');

		$this->addChild('ORMTest', 'command', 'qc_id', 'qc_id');

		$this->setConfigDbPrimaryKey	('qcl_id');
		$this->setConfigDbServer		('master');
		$this->setConfigObject			(__CLASS__);
		$this->setConfigDbTable			('d_queued_commands_logs');
	}
}

/**
 * Class ORMTestValidation
 * @property int id
 * @property string valString
 * @property decimal valDecimal
 * @property boolean valBoolean
 */
class ORMTestValidation extends ObjectRelationMapper_ORM
{
    protected function setORMStorages()
    {
        $this->configStorage 	= 'ObjectRelationMapper_ConfigStorage_Basic';

        $connector = new ObjectRelationMapper_Connector_PDO(new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB , DB_USER, DB_PASS, Array(PDO::ATTR_PERSISTENT => true)));
        $this->queryBuilder = new ObjectRelationMapper_QueryBuilder_DB($connector);
    }

    function setUp()
    {
        $this->addColumn('qc_int', 'id', 'int', '10');
        $this->addColumn('qc_string', 'valString', 'string', '10');
        $this->addColumn('qc_decimal', 'valDecimal', 'decimal', '5,2');
        $this->addColumn('qc_boolean', 'valBoolean', 'boolean', '1');

        $this->setConfigDbPrimaryKey	('qc_int');
        $this->setConfigDbServer		('master');
        $this->setConfigObject			(__CLASS__);
        $this->setConfigDbTable			('d_validate_types');
    }
}


