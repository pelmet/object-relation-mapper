<?php

class SearchTest extends PHPUnit_Framework_TestCase
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

        $this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        mysql_select_db(DB_DB, $this->connection);
        mysql_query($insert, $this->connection);
    }

    public function tearDown()
    {
        $delete = 'TRUNCATE TABLE d_queued_commands';
        mysql_query($delete, $this->connection);
    }

    public function testSearchExact()
    {
        $search = new ObjectRelationMapper\Search_Search(new ORMTest());
        $search->exact('status', 5);

        $results = $search->getResults();

        $this->assertNotEmpty($results);
        $this->assertEquals(5, $results[0]->id);
    }

	public function testSearchNotExact()
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 6,
					qc_time_start = 111,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_DB, $this->connection);
		mysql_query($insert, $this->connection);

		$search = new ObjectRelationMapper\Search_Search(new ORMTest());
		$search->notExact('startTime', 123456);

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(6, $results[0]->id);
	}

    public function testSearchEmpty()
    {
        $search = new ObjectRelationMapper\Search_Search(new ORMTest());

        $results = $search->getResults();

        $this->assertNotEmpty($results);
        $this->assertEquals(5, $results[0]->id);
    }

    public function testSearchFrom()
    {
        $search = new ObjectRelationMapper\Search_Search(new ORMTest());
        $search->from('startTime', 11);

        $results = $search->getResults();

        $this->assertNotEmpty($results);
        $this->assertEquals(5, $results[0]->id);
    }

    public function testSearchTo()
    {
        $search = new ObjectRelationMapper\Search_Search(new ORMTest());
        $search->to('startTime', 123456, true);

        $results = $search->getResults();

        $this->assertNotEmpty($results);
        $this->assertEquals(5, $results[0]->id);
    }

    public function testSearchLike()
    {
        $search = new ObjectRelationMapper\Search_Search(new ORMTest());
        $search->to('command', 'ls%');

        $results = $search->getResults();

        $this->assertNotEmpty($results);
        $this->assertEquals(5, $results[0]->id);
    }

    public function testSearchNotNull()
    {
        $search = new ObjectRelationMapper\Search_Search(new ORMTest());
        $search->notNull('command');

        $results = $search->getResults();

        $this->assertNotEmpty($results);
        $this->assertEquals(5, $results[0]->id);
    }

    public function testSearchNull()
    {
        $search = new ObjectRelationMapper\Search_Search(new ORMTest());
        $search->null('command');

        $results = $search->getResults();

        $this->assertEmpty($results);
    }

	public function testCountNothing()
	{
		$search = new ObjectRelationMapper\Search_Search(new ORMTest());
		$search->null('command');

		$results = $search->getCount();

		$this->assertEquals(0, $results);
	}

	public function testCount()
	{
		$search = new ObjectRelationMapper\Search_Search(new ORMTest());
		$search->notNull('command');

		$results = $search->getCount();

		$this->assertEquals(1, $results);
	}
}