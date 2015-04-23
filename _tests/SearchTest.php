<?php

class SearchTest extends CommonTestClass
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
	public function testSearchExact($testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->exact('status', 5);

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(5, $results[0]->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchNotExact($testOrm)
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 6,
					qc_time_start = 111,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
		mysqli_select_db($this->connection, DB_DB);
		mysqli_query($this->connection, $insert);

		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->notExact('startTime', 123456);

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(6, $results[0]->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchEmpty($testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(5, $results[0]->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchFrom($testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->from('startTime', 11);

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(5, $results[0]->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchTo($testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->to('startTime', 123456, true);

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(5, $results[0]->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchLike($testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->to('command', 'ls%');

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(5, $results[0]->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchNotNull($testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->notNull('command');

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(5, $results[0]->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchNull($testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->null('command');

		$results = $search->getResults();
		$this->assertEmpty($results);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testCountNothing($testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->null('command');

		$results = $search->getCount();

		$this->assertEquals(0, $results);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testCount($testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->notNull('command');

		$results = $search->getCount();

		$this->assertEquals(1, $results);
	}


	/**
	 */
	public function testSearchWithChildrenWithoutSearchChild()
	{
		$insert1 = 'INSERT INTO d_queued_commands SET
					qc_id = 7,
					qc_time_start = 111,
					qc_time_end = 12345678,
					qc_status = 11,
					qc_command = "ls -laf"';

		$insert2 = 'INSERT INTO d_queued_commands SET
					qc_id = 8,
					qc_time_start = 111,
					qc_time_end = 12345678,
					qc_status = 11,
					qc_command = "ls -laf"';

		$insert3 = 'INSERT INTO d_queued_commands_logs SET
					qcl_id = 11,
					qc_id = 7,
					qcl_text = "ls -laf"';

		$insert4 = 'INSERT INTO d_queued_commands_logs SET
					qcl_id = 13,
					qc_id = 7,
					qcl_text = "ls -laf"';

		$insert5 = 'INSERT INTO d_queued_commands_logs SET
					qcl_id = 12,
					qc_id = 8,
					qcl_text = "ls -laf"';

		$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
		mysqli_select_db($this->connection, DB_DB);
		mysqli_query($this->connection, $insert1);
		mysqli_query($this->connection, $insert2);
		mysqli_query($this->connection, $insert3);
		mysqli_query($this->connection, $insert4);
		mysqli_query($this->connection, $insert5);

		$testOrm = new ORMTest();

		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->exact('status', 11);
		$results = $search->getResultsWithChildrenLoaded();

		$this->assertNotEmpty($results);
		$this->assertEquals(7, $results[0]->id);
		$this->assertEquals(8, $results[1]->id);

		$this->assertTrue(!isset($results[1]->logs[0]));
		$this->assertTrue(!isset($results[1]->logs[1]));
	}

	/**
	 */
	public function testSearchWithChildrenWithSearchChild()
	{
		$insert1 = 'INSERT INTO d_queued_commands SET
					qc_id = 9,
					qc_time_start = 111,
					qc_time_end = 12345678,
					qc_status = 11,
					qc_command = "ls -laf"';

		$insert2 = 'INSERT INTO d_queued_commands SET
					qc_id = 10,
					qc_time_start = 111,
					qc_time_end = 12345678,
					qc_status = 11,
					qc_command = "ls -laf"';

		$insert3 = 'INSERT INTO d_queued_commands_logs SET
					qcl_id = 14,
					qc_id = 9,
					qcl_text = "ls -laf"';

		$insert4 = 'INSERT INTO d_queued_commands_logs SET
					qcl_id = 16,
					qc_id = 9,
					qcl_text = "ls -laf"';

		$insert5 = 'INSERT INTO d_queued_commands_logs SET
					qcl_id = 15,
					qc_id = 10,
					qcl_text = "ls -laf"';

		$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
		mysqli_select_db($this->connection, DB_DB);
		mysqli_query($this->connection, $insert1);
		mysqli_query($this->connection, $insert2);
		mysqli_query($this->connection, $insert3);
		mysqli_query($this->connection, $insert4);
		mysqli_query($this->connection, $insert5);

		$testOrm = new ORMTest();
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->exact('status', 11);
		$search->child('logs', "INNER");
		$results = $search->getResultsWithChildrenLoaded();

		$this->assertNotEmpty($results);
		$this->assertEquals(9, $results[0]->id);
		$this->assertEquals(10, $results[1]->id);

		$this->assertEquals(14, $results[0]->logs[0]->id);
		$this->assertEquals(16, $results[0]->logs[1]->id);
		$this->assertEquals(15, $results[1]->logs[0]->id);
		$this->assertFalse(isset($results[1]->logs[1]));
	}

	public function testSearchIn()
	{
		$insert1 = 'INSERT INTO d_queued_commands SET
					qc_id = 11,
					qc_time_start = 111,
					qc_time_end = 12345678,
					qc_status = 11,
					qc_command = "ls -laf"';

		$insert2 = 'INSERT INTO d_queued_commands SET
					qc_id = 12,
					qc_time_start = 111,
					qc_time_end = 12345678,
					qc_status = 11,
					qc_command = "ls -laf"';

		$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
		mysqli_select_db($this->connection, DB_DB);
		mysqli_query($this->connection, $insert1);
		mysqli_query($this->connection, $insert2);

		$testOrm = new ORMTest();
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->in('id', array(11, 5));
		$results = $search->getResults();

		$toCheck = array();
		foreach($results As $result){
			$toCheck[$result->id] = $result;
		}

		$this->assertNotEmpty($results);
		$this->assertEquals(sizeof($toCheck), sizeof($results));
		$this->assertTrue(isset($toCheck[5]));
		$this->assertTrue(isset($toCheck[11]));
		$this->assertFalse(isset($toCheck[12]));
	}

	public function testSearchNotIn()
	{
		$insert1 = 'INSERT INTO d_queued_commands SET
					qc_id = 13,
					qc_time_start = 111,
					qc_time_end = 12345678,
					qc_status = 11,
					qc_command = "ls -laf"';

		$insert2 = 'INSERT INTO d_queued_commands SET
					qc_id = 14,
					qc_time_start = 111,
					qc_time_end = 12345678,
					qc_status = 11,
					qc_command = "ls -laf"';

		$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
		mysqli_select_db($this->connection, DB_DB);
		mysqli_query($this->connection, $insert1);
		mysqli_query($this->connection, $insert2);

		$testOrm = new ORMTest();
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->notIn('id', array(5, 11, 12, 14));
		$results = $search->getResults();

		$toCheck = array();
		foreach($results As $result){
			$toCheck[$result->id] = $result;
		}

		$this->assertNotEmpty($results);
		$this->assertEquals(sizeof($toCheck), sizeof($results));
		$this->assertFalse(isset($toCheck[5]));
		$this->assertFalse(isset($toCheck[11]));
		$this->assertFalse(isset($toCheck[12]));
		$this->assertTrue(isset($toCheck[13]));
		$this->assertFalse(isset($toCheck[14]));
	}
}