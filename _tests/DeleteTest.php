<?php

class DeleteTest extends PHPUnit_Framework_TestCase
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

    public function testDeleteNotNow()
    {
        $testOrm = new ORMTest(5);
        $testOrm->delete();

        $query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_id = 5');
        $result = mysqli_fetch_assoc($query);

        $this->assertEquals('ls -laf', $result['qc_command']);
        $this->assertEquals('5', $result['qc_status']);
        $this->assertEquals(123456, $result['qc_time_start']);
        $this->assertEquals(12345678, $result['qc_time_end']);

        $testOrm = NULL;

        $query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_id = 5');
        $result = mysqli_fetch_assoc($query);

        $this->assertEmpty($result);
    }

    public function testDeleteNow()
    {
        $testOrm = new ORMTest(5);
        $testOrm->delete(true);

        $query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_id = 5');
        $result = mysqli_fetch_assoc($query);

        $this->assertEmpty($result);
    }

	public function testDeleteByOrm()
	{
		$testOrm = new ORMTest();
		$testOrm->startTime = 123456;
		$testOrm->endTime = 12345678;
		$testOrm->load();
		$this->assertTrue($testOrm->isLoaded());

		$testOrm = new ORMTest();
		$testOrm->startTime = 123456;
		$testOrm->endTime = 12345678;
		$testOrm->getQueryBuilder()->deleteByOrm($testOrm);

		$testOrm = new ORMTest();
		$testOrm->startTime = 123456;
		$testOrm->endTime = 12345678;
		$testOrm->load();
		$this->assertFalse($testOrm->isLoaded());
	}
}