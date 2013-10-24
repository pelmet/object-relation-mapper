<?php

class BasicFunctionalityTest extends PHPUnit_Framework_TestCase
{
	public function testBasic()
	{
		$testOrm = new ORMTest();

		$this->assertInstanceOf('ObjectRelationMapper_ORM_Interface', $testOrm);
		$this->assertInstanceOf('ObjectRelationMapper_ORM_Abstract', $testOrm);
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

	public function testCallMethod()
	{
		$testOrm = new ORMTest();

		$this->assertEquals('qc_id', $testOrm->getConfigDbPrimaryKey());
		$this->assertEquals('master', $testOrm->getConfigDbServer());
		$this->assertEquals('ORMTest', $testOrm->getConfigObject());
		$this->assertEquals('d_queued_commands', $testOrm->getConfigDbTable());
	}
}