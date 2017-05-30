<?php

class CountTest extends CommonTestClass
{
	public function setUp()
    {
        parent::setUp();

        // we need to prepare yaml fixture file for count test
        if(file_exists(BASE_DIR . '/Databases/yml-CountTest.yml')){
            $data = file_get_contents(BASE_DIR . '/Databases/yml-CountTest.yml');
            file_put_contents('/tmp/d_queued_commands.yml', $data);
        }
    }

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