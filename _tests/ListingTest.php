<?php

class ListingTest extends PHPUnit_Framework_TestCase
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

	public function testListingBasic()
	{
		$search = new ObjectRelationMapper_Search_Search(new ORMTest());
		$search->notNull('command');

		$table = new ObjectRelationMapper_Listing_Table();
		$table->addDataSource(new ObjectRelationMapper_Listing_Connector_Search($search));
		$worker = new ObjectRelationMapper_Listing_Column_Multi(' ');
		$worker->addColumn(new ObjectRelationMapper_Listing_Column_Basic('id'));
		$worker->addColumn(new ObjectRelationMapper_Listing_Column_Basic('startTime'));
		$table->addColumn('Worker', $worker);

		$result = $table->render();

		$this->assertNotEmpty($result);
	}
}