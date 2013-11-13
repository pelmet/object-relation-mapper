<?php

class LoadTest extends PHPUnit_Framework_TestCase
{
	protected $connection;

	public function setUp()
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 5,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_DB, $this->connection);
		mysql_query($insert, $this->connection);
	}

	public function tearDown()
	{
		$delete = 'TRUNCATE TABLE d_queued_commands';
		mysql_query($delete, $this->connection);
	}

	public function testLoadByData()
	{
		$testOrm = new ORMTest();
		$testOrm->status = 5;
		$testOrm->load();

		$this->assertEquals(5, $testOrm->id);
		$this->assertEquals(123456, $testOrm->startTime);
		$this->assertEquals(12345678, $testOrm->endTime);
		$this->assertEquals('ls -laf', $testOrm->command);
	}

	public function testLoadByPrimaryKey()
	{
		$testOrm = new ORMTest();
		$testOrm->primaryKey = 5;
		$testOrm->loadByPrimaryKey();

		$this->assertEquals(5, $testOrm->status);
		$this->assertEquals(123456, $testOrm->startTime);
		$this->assertEquals(12345678, $testOrm->endTime);
		$this->assertEquals('ls -laf', $testOrm->command);
	}

	public function testLoadByConstructor()
	{
		$testOrm = new ORMTest(5);

		$this->assertEquals(5, $testOrm->status);
		$this->assertEquals(123456, $testOrm->startTime);
		$this->assertEquals(12345678, $testOrm->endTime);
		$this->assertEquals('ls -laf', $testOrm->command);
	}

	public function testLoadMultipleFromDb()
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 6,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		mysql_query($insert, $this->connection);

		$testOrm = new ORMTest();
		$testOrm->status = 5;
		$collection = $testOrm->loadMultiple();

		$this->assertEquals(2, count($collection));
		foreach($collection as $singleOrm){
			$this->assertInstanceOf('ORMTest', $singleOrm);
			$this->assertEquals(5, $singleOrm->status);
		}
	}

	public function testLoadMultipleFromArrayResult()
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 6,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		mysql_query($insert, $this->connection);

		$query = mysql_query('SELECT * FROM d_queued_commands WHERE qc_status = 5', $this->connection);

		$result = Array();
		while($row = mysql_fetch_assoc($query)){
			$result[] = $row;
		}

		$testOrm = new ORMTest();
		$collection = $testOrm->loadMultiple($result);

		$this->assertEquals(2, count($collection));
		foreach($collection as $singleOrm){
			$this->assertInstanceOf('ORMTest', $singleOrm);
			$this->assertEquals(5, $singleOrm->status);
		}
	}

	public function testLoadFromArrayWithIncompatibleProperties()
	{
		$testData =  Array ('qc_id' => 6,
            'qc_time_start' => 123456,
            'qc_time_end' => 12345678,
            'qc_status' => 5,
            'qc_command' => 'ls -laf',
			'iblah' => 'iblaaaah');

		$testOrm = new ORMTest();
		$testOrm->load($testData);

		$this->assertEquals(5, $testOrm->status);
		$this->assertEquals(123456, $testOrm->startTime);
		$this->assertEquals(12345678, $testOrm->endTime);
		$this->assertEquals('ls -laf', $testOrm->command);

		try{
			$this->assertEquals('iblaaah', $testOrm->iblah);
		} catch (Exception $e){
			$this->assertInstanceOf('Exception', $e);
		}
	}

    public function testLoadWithEmptyArray()
    {
        $testData =  Array ();

        $testOrm = new ORMTest();
        $testOrm->load($testData);

        $this->assertEquals(false, $testOrm->isLoaded());
    }

    public function testLoadMultipleWithEmptyArray()
    {
        $testData =  Array ();

        $testOrm = new ORMTest();
        $testOrm->loadMultiple($testData);

        $this->assertEquals(false, $testOrm->isLoaded());
    }
}