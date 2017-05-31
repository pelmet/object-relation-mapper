<?php

class ChildrenTest extends CommonTestClass
{
    /**
     * @dataProvider providerBasic
     */
	public function testChildNoLink($connector, $testOrm)
	{
		$this->assertEmpty($testOrm->children('logs'));
	}

    /**
     * @dataProvider providerBasic
     */
	public function testChildLink($connector, $testOrm)
	{
		$testOrm->primaryKey = 5;
		$this->assertNotEmpty($testOrm->children('logs'));
	}

    /**
     * @dataProvider providerBasic
     */
	public function testChildProperty($connector, $testOrm)
	{
        $testOrm->primaryKey = 5;
		$this->assertEquals('ls -laf', $testOrm->cProperty('logs.text'));
	}

    /**
     * @dataProvider providerBasic
     */
	public function testChildProperties($connector, $testOrm)
	{
		$testOrm->primaryKey = 5;
		$this->assertEquals('ls -laf', $testOrm->cProperties('logs.text', ' '));
	}

	/**
	 * @expectedException ObjectRelationMapper\Exception\ORM
	 */
	public function testAddColumnProtectedProperty()
	{
		$orm = new \ObjectRelationMapper\Tests\ORMBadChildProperty();
	}
}