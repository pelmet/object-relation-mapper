<?php

class CountTest extends CommonTestClass
{
	protected $connection;

	public function setUp()
	{
		$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
		mysqli_select_db($this->connection, DB_DB);

		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 5,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		mysqli_query($this->connection, $insert);

		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 6,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

        mysqli_query($this->connection, $insert);

		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 7,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

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
	public function testCountPrimaryKey($testOrm)
	{
		$testOrm->id = 5;
		$this->assertEquals(1, $testOrm->count());
	}

    /**
     * @dataProvider providerBasic
     */
	public function testCountByData($testOrm)
	{
		$testOrm->status = 5;
		$this->assertEquals(3, $testOrm->count());
	}
}