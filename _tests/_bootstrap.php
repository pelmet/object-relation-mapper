<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'ormtestuser');
define('DB_PASS', 'testpass');
define('DB_DB', 'orm_test_db');

require_once __DIR__ . '/CommonTestClass.php';

function autoload($className)
{
	$className = preg_replace('/^ObjectRelationMapper/', '', $className);
	$className = str_replace('\\', '/', $className);
	$className = str_replace('_', '/', $className);

	if (is_file(__DIR__ . '/../' . '/' . $className . '.php')) {
		require_once(__DIR__ . '/../' . '/' . $className . '.php');
	}

}

spl_autoload_register('autoload');

/**
 * Class ORMTest
 * @property int id
 * @property string startTime
 * @property string endTime
 * @property int status
 * @property string command
 */
class ORMTest extends ObjectRelationMapper\ORM
{
	protected function setORMStorages()
	{
		$this->configStorage = 'ObjectRelationMapper\ConfigStorage\Basic';

		$connector = new ObjectRelationMapper\Connector\PDO(new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB, DB_USER, DB_PASS, Array(PDO::ATTR_PERSISTENT => true)));
		$this->queryBuilder = new ObjectRelationMapper\QueryBuilder\DB($connector);
	}

	protected function setUp()
	{
		$this->addColumn('qc_id', 'id', 'int', '10');
		$this->addColumn('qc_time_start', 'startTime', 'int', '12');
		$this->addColumn('qc_time_end', 'endTime', 'int', '12');
		$this->addColumn('qc_status', 'status', 'int', '1');
		$this->addColumn('qc_command', 'command', 'string', '2000');

		$this->addDataAlias('statusStart', function ($orm) {
			return $orm->status . $orm->startTime;
		});
		$this->addDataAlias('startEndTime', 'startTime, endTime', ' ');

		$this->addChild('ORMTestChild', 'logs', 'qc_id', 'qc_id');

		$this->setConfigDbPrimaryKey('qc_id');
		$this->setConfigDbServer('master');
		$this->setConfigObject(__CLASS__);
		$this->setConfigDbTable('d_queued_commands');

		$this->setupMFU('id', 'test-data');
		$this->setupMFU('startTime', 'test-data');
	}
}

/**
 * Class ORMTest
 * @property int id
 * @property string startTime
 * @property string endTime
 * @property int status
 * @property string command
 */
class ORMTestOld extends ObjectRelationMapper\DataObjects
{
	protected function setORMStorages()
	{
		$this->configStorage = 'ObjectRelationMapper\ConfigStorage\Basic';

		$connector = new ObjectRelationMapper\Connector\PDO(new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB, DB_USER, DB_PASS, Array(PDO::ATTR_PERSISTENT => true)));
		$this->queryBuilder = new ObjectRelationMapper\QueryBuilder\DB($connector);
	}

	function setUp()
	{
		$this->config['rows'][] = Array('name' => 'qc_id', 'alias' => 'id');
		$this->config['rows'][] = Array('name' => 'qc_time_start', 'alias' => 'startTime');
		$this->config['rows'][] = Array('name' => 'qc_time_end', 'alias' => 'endTime');
		$this->config['rows'][] = Array('name' => 'qc_status', 'alias' => 'status');
		$this->config['rows'][] = Array('name' => 'qc_command', 'alias' => 'command');

		$this->config['server'] = 'master';
		$this->config['tableName'] = 'd_queued_commands';
		$this->config['primaryKey'] = 'qc_id';
		$this->config['object'] = __CLASS__;

		$this->config['child'][] = Array('name' => 'logs',
			'object' => 'ORMTestChild',
			'possibilities' => 'many',
			'delete' => false,
			Array(
				'localKey' => 'qc_id',
				'foreignKey' => 'qc_id'
			));

		$this->addDataAlias('statusStart', function ($orm) {
			return $orm->status . $orm->startTime;
		});
		$this->addDataAlias('startEndTime', 'startTime, endTime', ' ');

		$this->setupMFU('id', 'test-data');
		$this->setupMFU('startTime', 'test-data');
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
class ORMTestChild extends ObjectRelationMapper\ORM
{
	protected function setORMStorages()
	{
		$this->configStorage = 'ObjectRelationMapper\ConfigStorage\Basic';

		$connector = new ObjectRelationMapper\Connector\PDO(new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB, DB_USER, DB_PASS, Array(PDO::ATTR_PERSISTENT => true)));
		$this->queryBuilder = new ObjectRelationMapper\QueryBuilder\DB($connector);
	}

	protected function setUp()
	{
		$this->addColumn('qcl_id', 'id', 'int', '10');
		$this->addColumn('qc_id', 'queuedCommandId', 'int', '12');
		$this->addColumn('qcl_text', 'text', 'string', '2000');

		$this->addChild('ORMTest', 'command', 'qc_id', 'qc_id');

		$this->setConfigDbPrimaryKey('qcl_id');
		$this->setConfigDbServer('master');
		$this->setConfigObject(__CLASS__);
		$this->setConfigDbTable('d_queued_commands_logs');
	}
}

/**
 * Class ORMTestValidation
 * @property int id
 * @property string valString
 * @property decimal valDecimal
 * @property boolean valBoolean
 * @property string valDate
 * @property string valTime
 * @property string valText
 * @property string valChar
 * @property string valEnum
 */
class ORMTestValidation extends ObjectRelationMapper\ORM
{
	protected function setORMStorages()
	{
		$this->configStorage = 'ObjectRelationMapper\ConfigStorage\Basic';

		$connector = new ObjectRelationMapper\Connector\PDO(new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB, DB_USER, DB_PASS, Array(PDO::ATTR_PERSISTENT => true)));
		$this->queryBuilder = new ObjectRelationMapper\QueryBuilder\DB($connector);
	}

	protected function setUp()
	{
		$this->addColumn('qc_int', 'id', 'int', '10');
		$this->addColumn('qc_string', 'valString', 'string', '10');
		$this->addColumn('qc_decimal', 'valDecimal', 'decimal', '5,2');
		$this->addColumn('qc_boolean', 'valBoolean', 'boolean', '1');
		$this->addColumn('qc_date', 'valDate', 'date');
		$this->addColumn('qc_time', 'valTime', 'timestamp');
		$this->addColumn('qc_text', 'valText', 'text');
		$this->addColumn('qc_char', 'valChar', 'char', 3);
		$this->addColumn('qc_enum', 'valEnum', 'enum', ['abc','def','ghi','jkl']);

		$this->setConfigDbPrimaryKey('qc_int');
		$this->setConfigDbServer('master');
		$this->setConfigObject(__CLASS__);
		$this->setConfigDbTable('d_validate_types');
	}
}

/**
 * Class ORMTestValidation
 * @property int id
 * @property string valString
 * @property decimal valDecimal
 * @property boolean valBoolean
 */
class ORMBadProperty extends ObjectRelationMapper\ORM
{
	protected function setORMStorages()
	{
		$this->configStorage = 'ObjectRelationMapper\ConfigStorage\Basic';

		$connector = new ObjectRelationMapper\Connector\PDO(new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB, DB_USER, DB_PASS, Array(PDO::ATTR_PERSISTENT => true)));
		$this->queryBuilder = new ObjectRelationMapper\QueryBuilder\DB($connector);
	}

	protected function setUp()
	{
		$this->addColumn('qc_int', 'data', 'int', '10');

		$this->setConfigDbPrimaryKey('qc_int');
		$this->setConfigDbServer('master');
		$this->setConfigObject(__CLASS__);
		$this->setConfigDbTable('d_validate_types');
	}
}

/**
 * Class ORMBadChildProperty
 * @property int id
 * @property string valString
 * @property decimal valDecimal
 * @property boolean valBoolean
 */
class ORMBadChildProperty extends ObjectRelationMapper\ORM
{
	protected function setORMStorages()
	{
		$this->configStorage = 'ObjectRelationMapper\ConfigStorage\Basic';

		$connector = new ObjectRelationMapper\Connector\PDO(new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB, DB_USER, DB_PASS, Array(PDO::ATTR_PERSISTENT => true)));
		$this->queryBuilder = new ObjectRelationMapper\QueryBuilder\DB($connector);
	}

	protected function setUp()
	{
		$this->addColumn('qc_int', 'iblah', 'int', '10');
		$this->addChild('ORMTest', 'data', 'qc_id', 'qc_id');

		$this->setConfigDbPrimaryKey('qc_int');
		$this->setConfigDbServer('master');
		$this->setConfigObject(__CLASS__);
		$this->setConfigDbTable('d_validate_types');
	}
}

