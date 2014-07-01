<?php

class SaveTest extends PHPUnit_Framework_TestCase
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

    public function testInsert()
    {
        $testOrm = new ORMTest();
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

    public function testUpdate()
    {
        $insert = 'INSERT INTO d_queued_commands SET
					qc_id = 5,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

        mysqli_query($this->connection, $insert);

        $testOrm = new ORMTest(5);
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

    public function testForceInsert()
    {
        $testOrm = new ORMTest();
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

	public function testReadOnly()
	{
		$testOrm = new ORMTest();
		$testOrm->setReadOnly();
		$testOrm->id = 6;
		$testOrm->status = 10;
		$testOrm->command = 'ls -l';
		$testOrm->save(true);

		$query = mysqli_query($this->connection, 'SELECT * FROM d_queued_commands WHERE qc_status = 10 AND qc_command = "ls -l"');
		$result = mysqli_fetch_assoc($query);

		$this->assertEmpty($result);
	}

	public function testUpdateChangedPrimaryKey()
	{
		$insert = 'INSERT INTO d_queued_commands SET
					qc_id = 5,
					qc_time_start = 123456,
					qc_time_end = 12345678,
					qc_status = 5,
					qc_command = "ls -laf"';

		mysqli_query($this->connection, $insert);

		$testOrm = new ORMTest(5);
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

}