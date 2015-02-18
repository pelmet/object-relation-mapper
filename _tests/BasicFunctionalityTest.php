<?php

class BasicFunctionalityTest extends CommonTestClass
{
	/**
	 * @dataProvider providerBasic
	 */
	public function testBasic($testOrm)
	{
		$this->assertInstanceOf('ObjectRelationMapper\Base\AORM', $testOrm);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSetPrimaryKey($testOrm)
	{
		$testOrm->primaryKey = 125;

		$this->assertEquals(125, $testOrm->id);
		$this->assertEquals($testOrm->primaryKey, $testOrm->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSetterAndGetter($testOrm)
	{
		$testOrm->status = 5;
		$testOrm->command = 'ls -la';
		$testOrm->endTime = '123456';

		$this->assertEquals(5, $testOrm->status);
		$this->assertEquals('ls -la', $testOrm->command);
		$this->assertEquals('123456', $testOrm->endTime);
	}

	/**
	 * @expectedException ObjectRelationMapper\Exception\ORM
	 * @dataProvider providerBasic
	 */
	public function testSetterToBadColumn($testOrm)
	{
		$testOrm->iblah = 5;
	}

	/**
	 * @expectedException ObjectRelationMapper\Exception\ORM
	 * @dataProvider providerBasic
	 */
	public function testGetterToBadColumn($testOrm)
	{
		$testOrm->iblah;
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testCallMethod($testOrm)
	{
		$this->assertEquals('qc_id', $testOrm->getConfigDbPrimaryKey());
		$this->assertEquals('master', $testOrm->getConfigDbServer());
		$this->assertContains('ORMTest', $testOrm->getConfigObject());
		$this->assertEquals('d_queued_commands', $testOrm->getConfigDbTable());
	}

	/**
	 * @expectedException ObjectRelationMapper\Exception\ORM
	 * @dataProvider providerBasic
	 */
	public function testDynamicNotDefinedFunction($testOrm)
	{
		$testOrm->iblahIsChanged();
	}

	/**
	 * @expectedException ObjectRelationMapper\Exception\ORM
	 */
	public function testAddColumnProtectedProperty()
	{
		$orm = new ORMBadProperty();
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testPropertyHasChanged($testOrm)
	{
		$this->assertEquals(false, $testOrm->primaryKeyIsChanged());

		$testOrm->id = 5;

		$this->assertEquals(true, $testOrm->primaryKeyIsChanged());

		$testOrm->save();

		$this->assertEquals(false, $testOrm->primaryKeyIsChanged());
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testFieldNameAsCallFunction($testOrm)
	{
		$this->assertEquals('qc_id', $testOrm->id());
		$this->assertEquals('qc_time_end', $testOrm->endTime());
		$this->assertEquals('qc_status', $testOrm->status());
		$this->assertEquals('qc_command', $testOrm->command());
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testFieldNameWithTableAsCallFunction($testOrm)
	{
		$this->assertEquals('d_queued_commands.qc_id', $testOrm->idFull());
		$this->assertEquals('d_queued_commands.qc_time_end', $testOrm->endTimeFull());
		$this->assertEquals('d_queued_commands.qc_status', $testOrm->statusFull());
		$this->assertEquals('d_queued_commands.qc_command', $testOrm->commandFull());
	}

	/**
	 * @expectedException ObjectRelationMapper\Exception\ORM
	 * @dataProvider providerBasic
	 */
	public function testDynamicFieldNameAsCallFunctionNotDefined($testOrm)
	{
		$testOrm->iblahfield();
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testDataAliasClosure($testOrm)
	{
		$testOrm->status = 5;
		$testOrm->command = 'ls -la';
		$testOrm->endTime = '123456';
		$testOrm->startTime = '987';

		$this->assertEquals('5987', $testOrm->statusStart);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testDataAliasDelimiterString($testOrm)
	{
		$testOrm->status = 5;
		$testOrm->command = 'ls -la';
		$testOrm->endTime = '123456';
		$testOrm->startTime = '987';

		$this->assertEquals('987 123456', $testOrm->startEndTime);
	}
}