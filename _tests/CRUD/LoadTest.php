<?php

class LoadTest extends CommonTestClass
{
	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadByData($connector, $testOrm)
	{
		$testOrm->status = 5;
		$testOrm->load();

		$this->assertEquals(5, $testOrm->id);
		$this->assertEquals(123456, $testOrm->startTime);
		$this->assertEquals(12345678, $testOrm->endTime);
		$this->assertEquals('ls -laf', $testOrm->command);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadByPrimaryKey($connector, $testOrm)
	{
		$testOrm->primaryKey = 5;
		$testOrm->loadByPrimaryKey();

		$this->assertEquals(5, $testOrm->status);
		$this->assertEquals(123456, $testOrm->startTime);
		$this->assertEquals(12345678, $testOrm->endTime);
		$this->assertEquals('ls -laf', $testOrm->command);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadByConstructor($connector, $testOrm)
	{
	    $qb = $testOrm->getQueryBuilder();

		$testOrm = get_class($testOrm);
		$testOrm = new $testOrm(5, $qb);

		$this->assertEquals(5, $testOrm->status);
		$this->assertEquals(123456, $testOrm->startTime);
		$this->assertEquals(12345678, $testOrm->endTime);
		$this->assertEquals('ls -laf', $testOrm->command);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadMultipleFromDb($connector, $testOrm)
	{
		$testOrm->status = 5;
		$collection = $testOrm->loadMultiple();

		$this->assertEquals(3, count($collection));
		foreach ($collection as $singleOrm) {
			$this->assertInstanceOf(get_class($testOrm), $singleOrm);
			$this->assertEquals(5, $singleOrm->status);
		}
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadMultipleFromArrayResult($connector, $testOrm)
	{
        if($this->isFileConnector($connector)){
            $result = $this->fileConnectorGetFileData($connector, $testOrm->getQueryBuilder()->getFilename($testOrm));
        } else {
            $db = $this->getConnection($connector);
            $result = $db->query('SELECT * FROM d_queued_commands WHERE qc_status = 5', \PDO::FETCH_ASSOC)->fetchAll();
        }

		$collection = $testOrm->loadMultiple($result);

		$this->assertEquals(3, count($collection));
		foreach ($collection as $singleOrm) {
			$this->assertInstanceOf(get_class($testOrm), $singleOrm);
			$this->assertEquals(5, $singleOrm->status);
		}
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadFromArrayWithIncompatibleProperties($connector, $testOrm)
	{
		$testData = Array('qc_id' => 6,
			'qc_time_start' => 123456,
			'qc_time_end' => 12345678,
			'qc_status' => 5,
			'qc_command' => 'ls -laf',
			'iblah' => 'iblaaaah');

		$testOrm->load($testData);

		$this->assertEquals(5, $testOrm->status);
		$this->assertEquals(123456, $testOrm->startTime);
		$this->assertEquals(12345678, $testOrm->endTime);
		$this->assertEquals('ls -laf', $testOrm->command);

		try {
			$this->assertEquals('iblaaah', $testOrm->iblah);
		} catch (Exception $e) {
			$this->assertInstanceOf('Exception', $e);
		}
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadWithEmptyArray($connector, $testOrm)
	{
		$testData = Array();

		$testOrm->load($testData);

		$this->assertEquals(false, $testOrm->isLoaded());
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadMultipleWithEmptyArray($connector, $testOrm)
	{
		$testData = Array();

		$testOrm->loadMultiple($testData);

		$this->assertEquals(false, $testOrm->isLoaded());
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadMFU($connector, $testOrm)
	{
		$testOrm->loadMFU('test-data');

		$this->assertEquals(true, $testOrm->isLoaded());
		$this->assertEquals(5, $testOrm->id);
		$this->assertEquals(123456, $testOrm->startTime);
		$this->assertEquals(NULL, $testOrm->command);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadMultipleMFU($connector, $testOrm)
	{
		$results = $testOrm->loadMultipleMFU('test-data');

		$this->assertEquals(true, $results[0]->isLoaded());
		$this->assertEquals(5, $results[0]->id);
		$this->assertEquals(123456, $results[0]->startTime);
		$this->assertEquals(NULL, $results[0]->command);
	}
}