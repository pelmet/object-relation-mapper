<?php

class DataExchangeTest extends CommonTestClass
{
	/**
	 * @dataProvider providerBasic
	 */
	public function testArrayExchangeLoad($testOrm)
	{
		$mergeArray = Array(
			'id' => '1',
			'command' => 'iblah',
			'iblah' => 'iblah'
		);

		$merge = new ObjectRelationMapper\DataExchange\Arr($testOrm);
		$merge->addExclude('id');
		$merge->load($mergeArray);

		$this->assertEquals('iblah', $testOrm->command);
		$this->assertEquals(NULL, $testOrm->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testArrayExchangeExport($testOrm)
	{
		$testOrm->command = 'iblah';
		$testOrm->endTime = '123456';
		$testOrm->startTime = '123';

		$merge = new ObjectRelationMapper\DataExchange\Arr($testOrm);
		$array = $merge->export();

		$this->assertEquals('iblah', $array['command']);
		$this->assertEquals('123456', $array['endTime']);
		$this->assertEquals('123', $array['startTime']);
	}

}