<?php

class BasicFunctionalityTest extends PHPUnit_Framework_TestCase
{
	public function testBasic()
	{
		$testOrm = new ORMTest();

		$this->assertInstanceOf('ObjectRelationMapper\ORM_Interface', $testOrm);
		$this->assertInstanceOf('ObjectRelationMapper\ORM_Abstract', $testOrm);
	}

	public function testSetPrimaryKey()
	{
		$testOrm = new ORMTest();

		$testOrm->primaryKey = 125;

		$this->assertEquals(125, $testOrm->id);
		$this->assertEquals($testOrm->primaryKey, $testOrm->id);
	}

	public function testSetterAndGetter()
	{
		$testOrm = new ORMTest();

		$testOrm->status = 5;
		$testOrm->command = 'ls -la';
		$testOrm->endTime = '123456';

		$this->assertEquals(5, $testOrm->status);
		$this->assertEquals('ls -la', $testOrm->command);
		$this->assertEquals('123456', $testOrm->endTime);
	}

	/**
	 * @expectedException ObjectRelationMapper\Exception_ORM
	 */
	public function testSetterToBadColumn()
	{
		$testOrm = new ORMTest();

		$testOrm->iblah = 5;
	}

	/**
	 * @expectedException ObjectRelationMapper\Exception_ORM
	 */
	public function testGetterToBadColumn()
	{
		$testOrm = new ORMTest();

		$testOrm->iblah;
	}

	public function testCallMethod()
	{
		$testOrm = new ORMTest();

		$this->assertEquals('qc_id', $testOrm->getConfigDbPrimaryKey());
		$this->assertEquals('master', $testOrm->getConfigDbServer());
		$this->assertEquals('ORMTest', $testOrm->getConfigObject());
		$this->assertEquals('d_queued_commands', $testOrm->getConfigDbTable());
	}

	/**
	 * @expectedException ObjectRelationMapper\Exception_ORM
	 */
	public function testDynamicNotDefinedFunction()
	{
		$testOrm = new ORMTest();

		$testOrm->iblahIsChanged();
	}

	public function testPropertyHasChanged()
	{
		$testOrm = new ORMTest();

		$this->assertEquals(false, $testOrm->primaryKeyIsChanged());

		$testOrm->id = 5;

		$this->assertEquals(true, $testOrm->primaryKeyIsChanged());

		$testOrm->save();

		$this->assertEquals(false, $testOrm->primaryKeyIsChanged());
	}

	public function testFieldNameAsCallFunction()
	{
		$testOrm = new ORMTest();

		$this->assertEquals('qc_id', $testOrm->id());
		$this->assertEquals('qc_time_end', $testOrm->endTime());
		$this->assertEquals('qc_status', $testOrm->status());
		$this->assertEquals('qc_command', $testOrm->command());
	}

	public function testFieldNameWithTableAsCallFunction()
	{
		$testOrm = new ORMTest();

		$this->assertEquals('d_queued_commands.qc_id', $testOrm->idFull());
		$this->assertEquals('d_queued_commands.qc_time_end', $testOrm->endTimeFull());
		$this->assertEquals('d_queued_commands.qc_status', $testOrm->statusFull());
		$this->assertEquals('d_queued_commands.qc_command', $testOrm->commandFull());
	}

	/**
	 * @expectedException ObjectRelationMapper\Exception_ORM
	 */
	public function testDynamicFieldNameAsCallFunctionNotDefined()
	{
		$testOrm = new ORMTest();
		$testOrm->iblahfield();
	}

	public function testDataAliasClosure()
	{
		$testOrm = new ORMTest();

		$testOrm->status = 5;
		$testOrm->command = 'ls -la';
		$testOrm->endTime = '123456';
		$testOrm->startTime = '987';

		$this->assertEquals('5987', $testOrm->statusStart);
	}

	public function testDataAliasDelimiterString()
	{
		$testOrm = new ORMTest();

		$testOrm->status = 5;
		$testOrm->command = 'ls -la';
		$testOrm->endTime = '123456';
		$testOrm->startTime = '987';

		$this->assertEquals('987 123456', $testOrm->startEndTime);
	}
}