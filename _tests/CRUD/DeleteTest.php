<?php

class DeleteTest extends CommonTestClass
{
    public function setUp()
    {
        parent::setUp();

    }

	/**
	 * @dataProvider providerBasic
	 */
	public function testDeleteNotNow($connector, $testOrm)
	{
        /**
         * @var $testOrm \ObjectRelationMapper\ORM
         */

        $testOrm->primaryKey = 5;
        $testOrm->load();
		$testOrm->delete();

        $db = $this->getConnection($connector);
        $result = $db->query('SELECT * FROM d_queued_commands WHERE qc_id = 5', \PDO::FETCH_ASSOC)->fetchAll();
        $result = $result[0];

		$this->assertEquals('ls -laf', $result['qc_command']);
		$this->assertEquals('5', $result['qc_status']);
		$this->assertEquals(123456, $result['qc_time_start']);
		$this->assertEquals(12345678, $result['qc_time_end']);

		$testOrm->__destruct();

        $result = $db->query('SELECT * FROM d_queued_commands WHERE qc_id = 5', \PDO::FETCH_ASSOC)->fetchAll();

		$this->assertEmpty($result);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testDeleteNow($connector, $testOrm)
	{
		$testOrm->primaryKey = 5;
		$testOrm->load();
		$testOrm->delete(true);

        $db = $this->getConnection($connector);
        $result = $db->query('SELECT * FROM d_queued_commands WHERE qc_id = 5', \PDO::FETCH_ASSOC)->fetchAll();

		$this->assertEmpty($result);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testDeleteByOrm($connector, $testOrm)
	{
		$testOrm->startTime = 123456;
		$testOrm->endTime = 12345678;
		$testOrm->load();
		$this->assertTrue($testOrm->isLoaded());

		$qb = $testOrm->getQueryBuilder();

		$testOrm = get_class($testOrm);
		$testOrm = new $testOrm(NULL, $qb);
		$testOrm->startTime = 123456;
		$testOrm->endTime = 12345678;
		$testOrm->getQueryBuilder()->deleteByOrm($testOrm);

		$testOrm = get_class($testOrm);
		$testOrm = new $testOrm(NULL, $qb);
		$testOrm->startTime = 123456;
		$testOrm->endTime = 12345678;
		$testOrm->load();
		$this->assertFalse($testOrm->isLoaded());
	}
}