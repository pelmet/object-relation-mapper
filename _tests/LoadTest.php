<?php

class LoadTest extends CommonTestClass
{
	protected $connection;

	public function setUp()
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 5,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
		mysqli_select_db($this->connection, DB_DB);
		mysqli_query($this->connection, $insert);
	}

	public function tearDown()
	{
		$delete = 'TRUNCATE TABLE d_queued_commands';
		mysqli_query($this->connection, $delete);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadByData($testOrm)
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
	public function testLoadByPrimaryKey($testOrm)
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
	public function testLoadByConstructor($testOrm)
	{
		$testOrm = get_class($testOrm);
		$testOrm = new $testOrm(5);

		$this->assertEquals(5, $testOrm->status);
		$this->assertEquals(123456, $testOrm->startTime);
		$this->assertEquals(12345678, $testOrm->endTime);
		$this->assertEquals('ls -laf', $testOrm->command);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadMultipleFromDb($testOrm)
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 6,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

        mysqli_query($this->connection, $insert);

		$testOrm->status = 5;
		$collection = $testOrm->loadMultiple();

		$this->assertEquals(2, count($collection));
		foreach($collection as $singleOrm){
			$this->assertInstanceOf(get_class($testOrm), $singleOrm);
			$this->assertEquals(5, $singleOrm->status);
		}
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadMultipleFromArrayResult($testOrm)
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 6,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

        mysqli_query($this->connection, $insert);

		$query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_status = 5');

		$result = Array();
		while($row = mysqli_fetch_assoc($query)){
			$result[] = $row;
		}

		$collection = $testOrm->loadMultiple($result);

		$this->assertEquals(2, count($collection));
		foreach($collection as $singleOrm){
			$this->assertInstanceOf(get_class($testOrm), $singleOrm);
			$this->assertEquals(5, $singleOrm->status);
		}
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testLoadFromArrayWithIncompatibleProperties($testOrm)
	{
		$testData =  Array ('qc_id' => 6,
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

		try{
			$this->assertEquals('iblaaah', $testOrm->iblah);
		} catch (Exception $e){
			$this->assertInstanceOf('Exception', $e);
		}
	}

	/**
	 * @dataProvider providerBasic
	 */
    public function testLoadWithEmptyArray($testOrm)
    {
        $testData =  Array ();

        $testOrm->load($testData);

        $this->assertEquals(false, $testOrm->isLoaded());
    }

	/**
	 * @dataProvider providerBasic
	 */
    public function testLoadMultipleWithEmptyArray($testOrm)
    {
        $testData =  Array ();

        $testOrm->loadMultiple($testData);

        $this->assertEquals(false, $testOrm->isLoaded());
    }
}