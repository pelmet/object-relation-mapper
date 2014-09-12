<?php

class ChildrenTest extends PHPUnit_Framework_TestCase
{
	protected $connection;

	public function setUp()
	{
		$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
		mysqli_select_db($this->connection, DB_DB);

		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 5,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		mysqli_query($this->connection, $insert);

		$insert = 'INSERT INTO d_queued_commands_logs SET
					qc_id = 5,
					qcl_id = 2,
					qcl_text = "ls -laf"';

		mysqli_query($this->connection, $insert);
	}

	public function tearDown()
	{
		$delete = 'TRUNCATE TABLE d_queued_commands';
		mysqli_query($this->connection, $delete);

		$delete = 'TRUNCATE TABLE d_queued_commands_logs';
		mysqli_query($this->connection, $delete);
	}

	public function testChildNoLink()
	{
		$testOrm = new ORMTest();
		$this->assertEmpty($testOrm->children('logs'));
	}

	public function testChildLink()
	{
		$testOrm = new ORMTest(5);
		$this->assertNotEmpty($testOrm->children('logs'));
	}

	public function testChildProperty()
	{
		$testOrm = new ORMTest(5);

		$this->assertEquals('ls -laf', $testOrm->cProperty('logs.text'));
	}

	public function testChildProperties()
	{
		$testOrm = new ORMTest(5);

		$this->assertEquals('ls -laf', $testOrm->cProperties('logs.text', ' '));
	}
}