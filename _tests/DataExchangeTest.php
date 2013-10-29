<?php

class DataExchangeTest extends PHPUnit_Framework_TestCase
{
	protected $connection;

	public function setUp()
	{

	}

	public function tearDown()
	{

	}

	public function testArrayExchangeLoad()
	{
		$mergeArray = Array(
			'id' => '1',
			'command' => 'iblah',
			'iblah' => 'iblah'
		);

		$testOrm = new ORMTest();

		$merge = new ObjectRelationMapper_DataExchange_Array($testOrm);
		$merge->addExclude('id');
		$merge->load($mergeArray);

		$this->assertEquals('iblah', $testOrm->command);
		$this->assertEquals(NULL, $testOrm->id);
	}

	public function testArrayExchangeExport()
	{
		$testOrm = new ORMTest();
		$testOrm->command = 'iblah';
		$testOrm->endTime = '123456';
		$testOrm->startTime = '123';

		$merge = new ObjectRelationMapper_DataExchange_Array($testOrm);
		$array = $merge->export();

		$this->assertEquals('iblah', $array['command']);
		$this->assertEquals('123456', $array['endTime']);
		$this->assertEquals('123', $array['startTime']);
	}

}