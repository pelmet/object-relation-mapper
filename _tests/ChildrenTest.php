<?php

class ChildrenTest extends PHPUnit_Framework_TestCase
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

		$insert = 'INSERT INTO d_queued_commands_logs SET
					qc_id = 5,
					qcl_id = 2,
					qcl_text = "ls -laf"';

		mysql_query($insert, $this->connection);
	}

	public function tearDown()
	{
		$delete = 'TRUNCATE TABLE d_queued_commands';
		mysql_query($delete, $this->connection);

		$delete = 'TRUNCATE TABLE d_queued_commands_logs';
		mysql_query($delete, $this->connection);
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

        $this->assertEquals('ls -laf', $testOrm->cProperties('logs.text', ''));
    }
}