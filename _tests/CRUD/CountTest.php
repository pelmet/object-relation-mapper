<?php

class CountTest extends CommonTestClass
{

	/**
	 * @dataProvider providerBasic
	 */
	public function testCountPrimaryKey($connector, $testOrm)
	{
		$testOrm->id = 5;
		$this->assertEquals(1, $testOrm->count());
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testCountByData($connector, $testOrm)
	{
		$testOrm->status = 5;
		$this->assertEquals(3, $testOrm->count());
	}
}