<?php

class ValidateTest extends PHPUnit_Framework_TestCase
{
	protected $connection;

	public function setUp()
	{
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_DB, $this->connection);
		$this->tearDown();
	}

	public function tearDown()
	{
		$delete = 'TRUNCATE TABLE d_queued_commands';
		mysql_query($delete, $this->connection);
	}

	public function testValidateFalse()
	{
		$testOrm = new ORMTest();
		$testOrm->id = 5;
		$testOrm->status = 'iblah';

		$this->assertEquals(false, $testOrm->validate());
	}

	public function testValidateTrue()
	{
		$testOrm = new ORMTest();
		$testOrm->id = 5;
		$testOrm->status = 5;

		$this->assertEquals(true, $testOrm->validate());
	}
}