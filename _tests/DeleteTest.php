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

        $this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        mysql_select_db(DB_DB, $this->connection);
        mysql_query($insert, $this->connection);
    }

    public function tearDown()
    {
        $delete = 'TRUNCATE TABLE d_queued_commands';
        mysql_query($delete, $this->connection);
    }

    public function testDeleteNotNow()
    {
        $testOrm = new ORMTest(5);
        $testOrm->delete();

        $query = mysql_query('SELECT * FROM d_queued_commands WHERE qc_id = 5', $this->connection);
        $result = mysql_fetch_assoc($query);

        $this->assertEquals('ls -laf', $result['qc_command']);
        $this->assertEquals('5', $result['qc_status']);
        $this->assertEquals(123456, $result['qc_time_start']);
        $this->assertEquals(12345678, $result['qc_time_end']);

        $testOrm = NULL;

        $query = mysql_query('SELECT * FROM d_queued_commands WHERE qc_id = 5', $this->connection);
        $result = mysql_fetch_assoc($query);

        $this->assertEmpty($result);
    }

    public function testDeleteNow()
    {
        $testOrm = new ORMTest(5);
        $testOrm->delete(true);

        $query = mysql_query('SELECT * FROM d_queued_commands WHERE qc_id = 5', $this->connection);
        $result = mysql_fetch_assoc($query);

        $this->assertEmpty($result);
    }
}