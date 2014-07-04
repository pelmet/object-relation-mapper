<?php

class DeleteTest extends CommonTestClass
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
	public function testDeleteNotNow($testOrm)
    {
		$testOrm = get_class($testOrm);
		$testOrm = new $testOrm(5);
        $testOrm->delete();

        $query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_id = 5');
        $result = mysqli_fetch_assoc($query);

        $this->assertEquals('ls -laf', $result['qc_command']);
        $this->assertEquals('5', $result['qc_status']);
        $this->assertEquals(123456, $result['qc_time_start']);
        $this->assertEquals(12345678, $result['qc_time_end']);

        unset($testOrm);

        $query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_id = 5');
        $result = mysqli_fetch_assoc($query);

        $this->assertEmpty($result);
    }

	/**
	 * @dataProvider providerBasic
	 */
    public function testDeleteNow($testOrm)
    {
		$testOrm->primaryKey = 5;
		$testOrm->load();
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