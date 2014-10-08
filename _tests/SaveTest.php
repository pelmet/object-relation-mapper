<?php

class SaveTest extends CommonTestClass
{
	protected $connection;

	public function setUp()
	{
		$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
		mysqli_select_db($this->connection, DB_DB);

		$this->tearDown();
	}

	public function tearDown()
	{
		$delete = 'TRUNCATE TABLE d_queued_commands';
		mysqli_query($this->connection, $delete);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testInsert($testOrm)
	{
		$testOrm->status = 5;
		$testOrm->command = 'ls -l';
		$testOrm->save();

		$query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_status = 5 AND qc_command = "ls -l"');
		$result = mysqli_fetch_assoc($query);

		$this->assertEquals('ls -l', $result['qc_command']);
		$this->assertEquals('5', $result['qc_status']);
		$this->assertEquals(NULL, $result['qc_time_start']);
		$this->assertEquals(NULL, $result['qc_time_end']);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testUpdate($testOrm)
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 5,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		mysqli_query($this->connection, $insert);

		$class = get_class($testOrm);
		$testOrm = new $class(5);
		$testOrm->status = 10;
		$testOrm->command = 'ls -l';
		$testOrm->save();

		$query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_status = 10 AND qc_command = "ls -l"');
		$result = mysqli_fetch_assoc($query);

		$this->assertEquals('ls -l', $result['qc_command']);
		$this->assertEquals('10', $result['qc_status']);
		$this->assertEquals('123456', $result['qc_time_start']);
		$this->assertEquals('12345678', $result['qc_time_end']);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testForceInsert($testOrm)
	{
		$testOrm->id = 6;
		$testOrm->status = 10;
		$testOrm->command = 'ls -l';
		$testOrm->save(true);

		$query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_status = 10 AND qc_command = "ls -l"');
		$result = mysqli_fetch_assoc($query);

		$this->assertEquals('ls -l', $result['qc_command']);
		$this->assertEquals('10', $result['qc_status']);
		$this->assertEquals(NULL, $result['qc_time_start']);
		$this->assertEquals(NULL, $result['qc_time_end']);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testReadOnly($testOrm)
	{
		$testOrm->setReadOnly();
		$testOrm->id = 6;
		$testOrm->status = 10;
		$testOrm->command = 'ls -l';
		$testOrm->save(true);

		$query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_status = 10 AND qc_command = "ls -l"');
		$result = mysqli_fetch_assoc($query);

		$this->assertEmpty($result);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testUpdateChangedPrimaryKey($testOrm)
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 5,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		mysqli_query($this->connection, $insert);

		$class = get_class($testOrm);
		$testOrm = new $class(5);
		$testOrm->status = 10;
		$testOrm->command = 'ls -l';
		$testOrm->id = 15;
		$testOrm->save();

		$query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_status = 10 AND qc_command = "ls -l"');
		$result = mysqli_fetch_assoc($query);

		$this->assertEquals('ls -l', $result['qc_command']);
		$this->assertEquals('10', $result['qc_status']);
		$this->assertEquals('123456', $result['qc_time_start']);
		$this->assertEquals('12345678', $result['qc_time_end']);
		$this->assertEquals('15', $result['qc_id']);
	}

	/**
	 * @dataProvider providerUpdateChanged
	 */
	public function testUseOfChangedProperties($testOrm1)
	{
		$testOrm2 = clone $testOrm1;
		$testOrm3 = clone $testOrm1;
		/** @var  ORMTestUpdateFromChangedProperties $testOrm1 */
		/** @var  ORMTestUpdateFromChangedProperties $testOrm2 */
		/** @var  ORMTestUpdateFromChangedProperties $testOrm3 */

		$testOrm1->valName = 1;
		$testOrm1->valText = 2;
		$testOrm1->save();

		$testOrm2->id = $testOrm1->id;
		$testOrm2->load();

		$testOrm1->valName = 5;
		$testOrm1->save();

		$testOrm2->valText = 10;
		$testOrm2->save();

		$testOrm3->id = $testOrm1->id;
		$testOrm3->load();

		$this->assertEquals($testOrm1->id, $testOrm3->id);
		$this->assertEquals($testOrm2->id, $testOrm3->id);
		$this->assertEquals($testOrm3->valName, 5);
		$this->assertEquals($testOrm3->valText, 10);
	}

	/**
	 * @dataProvider providerUpdateAll
	 */
	public function testUseOfAllProperties($testOrm1)
	{
		$testOrm2 = clone $testOrm1;
		$testOrm3 = clone $testOrm1;
		/** @var  ORMTestUpdateFromAllProperties $testOrm1 */
		/** @var  ORMTestUpdateFromAllProperties $testOrm2 */
		/** @var  ORMTestUpdateFromAllProperties $testOrm3 */
		$testOrm1->valName = 1;
		$testOrm1->valText = 2;
		$testOrm1->save();

		$testOrm2->id = $testOrm1->id;
		$testOrm2->load();

		$testOrm1->valName = 5;
		$testOrm1->save();

		$testOrm2->valText = 10;
		$testOrm2->save();

		$testOrm3->id = $testOrm1->id;
		$testOrm3->load();

		$this->assertEquals($testOrm1->id, $testOrm3->id);
		$this->assertEquals($testOrm2->id, $testOrm3->id);
		$this->assertEquals($testOrm3->valName, 1);
		$this->assertEquals($testOrm3->valText, 10);
	}
}