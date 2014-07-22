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
}