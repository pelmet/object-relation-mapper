<?php

class CountTest extends PHPUnit_Framework_TestCase
{
	protected $connection;

	public function setUp()
	{
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_DB, $this->connection);

		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 5,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		mysql_query($insert, $this->connection);

		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 6,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		mysql_query($insert, $this->connection);
	}

	public function tearDown()
	{
		$delete = 'TRUNCATE TABLE d_queued_commands';
		mysql_query($delete, $this->connection);
	}

	public function testCountPrimaryKey()
	{
		$testOrm = new ORMTest();
		$testOrm->id = 5;
		$count = $testOrm->count();

		$this->assertEquals(1, $count);
	}

	public function testCountByData()
	{
		$testOrm = new ORMTest();
		$testOrm->status = 5;
		$count = $testOrm->count();

		$this->assertEquals(2, $count);
	}
}