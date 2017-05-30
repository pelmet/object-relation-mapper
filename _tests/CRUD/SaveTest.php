<?php

class SaveTest extends CommonTestClass
{
    public function setUp()
    {
        parent::setUp();

    }

	/**
	 * @dataProvider providerBasic
	 */
	public function testInsert($connector, $testOrm)
	{
		$testOrm->status = 5;
		$testOrm->command = 'ls -l';
		$testOrm->save();

        $db = $this->getConnection($connector);
        $result = $db->query('SELECT * FROM d_queued_commands WHERE qc_status = 5 AND qc_command = "ls -l"', \PDO::FETCH_ASSOC)->fetchAll();
        $result = $result[0];

		$this->assertEquals('ls -l', $result['qc_command']);
		$this->assertEquals('5', $result['qc_status']);
		$this->assertEquals(NULL, $result['qc_time_start']);
		$this->assertEquals(NULL, $result['qc_time_end']);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testUpdate($connector, $testOrm)
	{
	    $qb = $testOrm->getQueryBuilder();

		$class = get_class($testOrm);
		$testOrm = new $class(5, $qb);
		$testOrm->status = 10;
		$testOrm->command = 'ls -l';
		$testOrm->save();

        $db = $this->getConnection($connector);
        $result = $db->query('SELECT * FROM d_queued_commands WHERE qc_status = 10 AND qc_command = "ls -l"', \PDO::FETCH_ASSOC)->fetchAll();
        $result = $result[0];

		$this->assertEquals('ls -l', $result['qc_command']);
		$this->assertEquals('10', $result['qc_status']);
		$this->assertEquals('123456', $result['qc_time_start']);
		$this->assertEquals('12345678', $result['qc_time_end']);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testForceInsert($connector, $testOrm)
	{
		$testOrm->id = 6;
		$testOrm->status = 10;
		$testOrm->command = 'ls -l';
		$testOrm->save(true);

        $db = $this->getConnection($connector);
        $result = $db->query('SELECT * FROM d_queued_commands WHERE qc_status = 10 AND qc_command = "ls -l"', \PDO::FETCH_ASSOC)->fetchAll();
        $result = $result[0];

		$this->assertEquals('ls -l', $result['qc_command']);
		$this->assertEquals('10', $result['qc_status']);
		$this->assertEquals(NULL, $result['qc_time_start']);
		$this->assertEquals(NULL, $result['qc_time_end']);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testReadOnly($connector, $testOrm)
	{
		$testOrm->setReadOnly();
		$testOrm->id = 6;
		$testOrm->status = 10;
		$testOrm->command = 'ls -l';
		$testOrm->save(true);

        $db = $this->getConnection($connector);
        $result = $db->query('SELECT * FROM d_queued_commands WHERE qc_status = 10 AND qc_command = "ls -l"', \PDO::FETCH_ASSOC)->fetchAll();

		$this->assertEmpty($result);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testUpdateChangedPrimaryKey($connector, $testOrm)
	{
        $qb = $testOrm->getQueryBuilder();

		$class = get_class($testOrm);
		$testOrm = new $class(5, $qb);
		$testOrm->status = 10;
		$testOrm->command = 'ls -l';
		$testOrm->id = 15;
		$testOrm->save();

        $db = $this->getConnection($connector);
        $result = $db->query('SELECT * FROM d_queued_commands WHERE qc_status = 10 AND qc_command = "ls -l"', \PDO::FETCH_ASSOC)->fetchAll();
        $result = $result[0];

		$this->assertEquals('ls -l', $result['qc_command']);
		$this->assertEquals('10', $result['qc_status']);
		$this->assertEquals('123456', $result['qc_time_start']);
		$this->assertEquals('12345678', $result['qc_time_end']);
		$this->assertEquals('15', $result['qc_id']);
	}

}