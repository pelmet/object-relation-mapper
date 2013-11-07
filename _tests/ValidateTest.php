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
		$delete = 'TRUNCATE TABLE d_queued_commands; TRUNCATE TABLE d_validate_types;';
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

    public function testValidateInt()
    {
        $testOrm = new ORMTestValidation();
        $testOrm->id = 5;
        $this->assertEquals(true, $testOrm->validate('id'));
        $testOrm->id = '5';
        $this->assertEquals(true, $testOrm->validate('id'));
        $testOrm->id = 5.98;
        $this->assertEquals(false, $testOrm->validate('id'));
        $testOrm->id = 'five';
        $this->assertEquals(false, $testOrm->validate('id'));
        $testOrm->id = false;
        $this->assertEquals(false, $testOrm->validate('id'));
        $testOrm->id = Array();
        $this->assertEquals(false, $testOrm->validate('id'));
        $testOrm->id = new ORMTestValidation();
        $this->assertEquals(false, $testOrm->validate('id'));
    }

    public function testValidateString()
    {
        $testOrm = new ORMTestValidation();
        $testOrm->valString = 5;
        $this->assertEquals(false, $testOrm->validate('valString'));
        $testOrm->valString = '5';
        $this->assertEquals(true, $testOrm->validate('valString'));
        $testOrm->valString = 5.98;
        $this->assertEquals(false, $testOrm->validate('valString'));
        $testOrm->valString = 'five';
        $this->assertEquals(true, $testOrm->validate('valString'));
        $testOrm->valString = false;
        $this->assertEquals(false, $testOrm->validate('valString'));
        $testOrm->valString = Array();
        $this->assertEquals(false, $testOrm->validate('valString'));
        $testOrm->valString = new ORMTestValidation();
        $this->assertEquals(false, $testOrm->validate('valString'));
    }

    public function testValidateDecimal()
    {
        $testOrm = new ORMTestValidation();
        $testOrm->valDecimal = 5;
        $this->assertEquals(true, $testOrm->validate('valDecimal'));
        $testOrm->valDecimal = 5.;
        $this->assertEquals(true, $testOrm->validate('valDecimal'));
        $testOrm->valDecimal = '5';
        $this->assertEquals(true, $testOrm->validate('valDecimal'));
        $testOrm->valDecimal = 5.98;
        $this->assertEquals(true, $testOrm->validate('valDecimal'));
        $testOrm->valDecimal = 'five';
        $this->assertEquals(false, $testOrm->validate('valDecimal'));
        $testOrm->valDecimal = false;
        $this->assertEquals(false, $testOrm->validate('valDecimal'));
        $testOrm->valDecimal = Array();
        $this->assertEquals(false, $testOrm->validate('valDecimal'));
        $testOrm->valDecimal = new ORMTestValidation();
        $this->assertEquals(false, $testOrm->validate('valDecimal'));
    }

    public function testValidateBoolean()
    {
        $testOrm = new ORMTestValidation();
        $testOrm->valBoolean = 5;
        $this->assertEquals(false, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = '5';
        $this->assertEquals(false, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = 5.98;
        $this->assertEquals(false, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = 'five';
        $this->assertEquals(false, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = false;
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = Array();
        $this->assertEquals(false, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = new ORMTestValidation();
        $this->assertEquals(false, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = 't';
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = 'true';
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = 1;
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = 0;
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
    }
}