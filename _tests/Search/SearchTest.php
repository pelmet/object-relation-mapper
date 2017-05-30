<?php

class SearchTest extends CommonTestClass
{
	protected $connection;

	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchExact($connector, $testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->exact('status', 5);

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(5, $results[0]->id);
        $this->assertEquals(6, $results[1]->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchNotExact($connector, $testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->notExact('startTime', 123456);

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(6, $results[0]->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchEmpty($connector, $testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);

		$results = $search->getResults();

		$this->assertNotEmpty($results);
		$this->assertEquals(5, $results[0]->id);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testSearchFrom($connector, $testOrm)
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
	public function testSearchTo($connector, $testOrm)
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
	public function testSearchLike($connector, $testOrm)
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
	public function testSearchNotNull($connector, $testOrm)
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
	public function testSearchNull($connector, $testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->null('command');

		$results = $search->getResults();
		$this->assertEmpty($results);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testCountNothing($connector, $testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->null('command');

		$results = $search->getCount();

		$this->assertEquals(0, $results);
	}

	/**
	 * @dataProvider providerBasic
	 */
	public function testCount($connector, $testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->notNull('command');

		$results = $search->getCount();

		$this->assertEquals(4, $results);
	}


    /**
     * @dataProvider providerBasic
     */
	public function testSearchWithChildrenWithoutSearchChild($connector, $testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->exact('status', 11);
		$results = $search->getResultsWithChildrenLoaded();

		$this->assertNotEmpty($results);
		$this->assertEquals(7, $results[0]->id);
		$this->assertEquals(8, $results[1]->id);

		$this->assertTrue(!isset($results[1]->logs[0]));
		$this->assertTrue(!isset($results[1]->logs[1]));
	}

    /**
     * @dataProvider providerBasic
     */
	public function testSearchWithChildrenWithSearchChild($connector, $testOrm)
	{
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->exact('status', 11);
		$search->child('logs', "INNER");
		$results = $search->getResultsWithChildrenLoaded();

		$this->assertNotEmpty($results);
		$this->assertEquals(9, $results[0]->id);
		$this->assertEquals(10, $results[1]->id);

		$this->assertEquals(14, $results[0]->logs[0]->id);
		$this->assertEquals(16, $results[0]->logs[1]->id);
		$this->assertEquals(15, $results[1]->logs[0]->id);
		$this->assertFalse(isset($results[1]->logs[1]));
	}

    /**
     * @dataProvider providerBasic
     */
	public function testSearchIn($connector, $testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->in('id', array(7, 5));
		$results = $search->getResults();

		$toCheck = array();
		foreach($results As $result){
			$toCheck[$result->id] = $result;
		}

		$this->assertNotEmpty($results);
		$this->assertEquals(sizeof($toCheck), sizeof($results));
		$this->assertTrue(isset($toCheck[5]));
		$this->assertTrue(isset($toCheck[7]));
		$this->assertFalse(isset($toCheck[12]));
	}

    /**
     * @dataProvider providerBasic
     */
	public function testSearchNotIn($connector, $testOrm)
	{
		$search = new ObjectRelationMapper\Search\Search($testOrm);
		$search->notIn('id', array(5, 6, 7));
		$results = $search->getResults();

		$toCheck = array();
		foreach($results As $result){
			$toCheck[$result->id] = $result;
		}

		$this->assertNotEmpty($results);
		$this->assertEquals(sizeof($toCheck), sizeof($results));
		$this->assertFalse(isset($toCheck[5]));
		$this->assertFalse(isset($toCheck[6]));
		$this->assertFalse(isset($toCheck[7]));
		$this->assertTrue(isset($toCheck[8]));
	}
}