<?php

class ValidateTest extends CommonTestClass
{
    /**
     * @dataProvider providerBasic
     */
	public function testValidateFalse($connector, $testOrm)
	{
		$testOrm->id = 5;
		$testOrm->status = 'iblah';

		$this->assertEquals(false, $testOrm->validate());
	}

    /**
     * @dataProvider providerBasic
     */
	public function testValidateTrue($connector, $testOrm)
	{
		$testOrm->id = 5;
		$testOrm->status = 5;

		$this->assertEquals(true, $testOrm->validate());
	}

    /**
     * @dataProvider providerValidation
     */
	public function testValidateInt($connector, $testOrm)
	{
		$testOrm->id = 5;
		$this->assertEquals(true, $testOrm->validate('id'));
		$testOrm->id = '5';
		$this->assertEquals(true, $testOrm->validate('id'));
		$testOrm->id = 5.98;
		$this->assertEquals(false, $testOrm->validate('id'));
		$testOrm->id = 'five';
		$this->assertEquals(false, $testOrm->validate('id'));
		$testOrm->id = false;
		$this->assertEquals(false, $testOrm->validate('id'));
		$testOrm->id = Array();
		$this->assertEquals(false, $testOrm->validate('id'));
		$testOrm->id = new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('mysql'));
		$this->assertEquals(false, $testOrm->validate('id'));
	}

    /**
     * @dataProvider providerValidation
     */
	public function testValidateString($connector, $testOrm)
	{
		$testOrm->valString = 5;
		$this->assertEquals(false, $testOrm->validate('valString'));
		$testOrm->valString = '5';
		$this->assertEquals(true, $testOrm->validate('valString'));
		$testOrm->valString = 5.98;
		$this->assertEquals(false, $testOrm->validate('valString'));
		$testOrm->valString = 'five';
		$this->assertEquals(true, $testOrm->validate('valString'));
		$testOrm->valString = false;
		$this->assertEquals(false, $testOrm->validate('valString'));
		$testOrm->valString = Array();
		$this->assertEquals(false, $testOrm->validate('valString'));
		$testOrm->valString = new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('mysql'));
		$this->assertEquals(false, $testOrm->validate('valString'));
	}

    /**
     * @dataProvider providerValidation
     */
	public function testValidateDecimal($connector, $testOrm)
	{
		$testOrm->valDecimal = 5;
		$this->assertEquals(true, $testOrm->validate('valDecimal'));
		$testOrm->valDecimal = 5.;
		$this->assertEquals(true, $testOrm->validate('valDecimal'));
		$testOrm->valDecimal = '5';
		$this->assertEquals(true, $testOrm->validate('valDecimal'));
		$testOrm->valDecimal = 5.98;
		$this->assertEquals(true, $testOrm->validate('valDecimal'));
		$testOrm->valDecimal = 'five';
		$this->assertEquals(false, $testOrm->validate('valDecimal'));
		$testOrm->valDecimal = false;
		$this->assertEquals(false, $testOrm->validate('valDecimal'));
		$testOrm->valDecimal = Array();
		$this->assertEquals(false, $testOrm->validate('valDecimal'));
		$testOrm->valDecimal = new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('mysql'));
		$this->assertEquals(false, $testOrm->validate('valDecimal'));
	}

    /**
     * @dataProvider providerValidation
     */
	public function testValidateBoolean($connector, $testOrm)
	{
		$testOrm->valBoolean = 5;
		$this->assertEquals(false, $testOrm->validate('valBoolean'));
		$testOrm->valBoolean = '5';
		$this->assertEquals(false, $testOrm->validate('valBoolean'));
		$testOrm->valBoolean = 5.98;
		$this->assertEquals(false, $testOrm->validate('valBoolean'));
		$testOrm->valBoolean = 'five';
		$this->assertEquals(false, $testOrm->validate('valBoolean'));
		$testOrm->valBoolean = Array();
		$this->assertEquals(false, $testOrm->validate('valBoolean'));
		$testOrm->valBoolean = new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('mysql'));
		$this->assertEquals(false, $testOrm->validate('valBoolean'));

        $testOrm->valBoolean = false;
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = true;
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
		$testOrm->valBoolean = 'on';
		$this->assertEquals(true, $testOrm->validate('valBoolean'));
		$testOrm->valBoolean = 'off';
		$this->assertEquals(true, $testOrm->validate('valBoolean'));
		$testOrm->valBoolean = 1;
		$this->assertEquals(true, $testOrm->validate('valBoolean'));
		$testOrm->valBoolean = 0;
		$this->assertEquals(true, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = 'yes';
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = 'no';
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = '0';
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
        $testOrm->valBoolean = '1';
        $this->assertEquals(true, $testOrm->validate('valBoolean'));
	}

    /**
     * @dataProvider providerValidation
     */
    public function testValidateDate($connector, $testOrm)
    {
        $testOrm->valDate = 5;
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = '5';
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = 5.98;
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = 'five';
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = Array();
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('mysql'));
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = false;
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = true;
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = '2015-04-17';
        $this->assertEquals(true, $testOrm->validate('valDate'));
        $testOrm->valDate = '2015-4-7';
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = '2015-04-7';
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = '2015-4-17';
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = '1000-01-01';
        $this->assertEquals(true, $testOrm->validate('valDate'));
        $testOrm->valDate = '9999-12-31';
        $this->assertEquals(true, $testOrm->validate('valDate'));
        $testOrm->valDate = '0015-04-17';
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = '2015-00-17';
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = '2015-04-00';
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = '2015-13-17';
        $this->assertEquals(false, $testOrm->validate('valDate'));
        $testOrm->valDate = '2015-04-32';
        $this->assertEquals(false, $testOrm->validate('valDate'));
    }

    /**
     * @dataProvider providerValidation
     */
    public function testValidateTimestamp($connector, $testOrm)
    {
        $testOrm->valTime = 5;
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '5';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = 5.98;
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = 'five';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = Array();
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('mysql'));
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = false;
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = true;
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-04-17';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-4-7';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-04-7';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-4-17';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '1000-01-01';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '9999-12-31';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '0015-04-17';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-00-17';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-04-00';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-13-17';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-04-32';
        $this->assertEquals(false, $testOrm->validate('valTime'));

        $testOrm->valTime = '3015-04-17 18/16';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-01-17 9:30';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-04-01H12:55:28';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-12-17 7,5';
        $this->assertEquals(false, $testOrm->validate('valTime'));

        $testOrm->valTime = '2015-04-15 12:00:00';
        $this->assertEquals(true, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-12-17 23:59:59';
        $this->assertEquals(true, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-11-11 24:00:00';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-04-32 00:00:00';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-04-01 37:44:12';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-12-17 14:67:34';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2015-04-22 18:56:70';
        $this->assertEquals(false, $testOrm->validate('valTime'));
        $testOrm->valTime = '2016-04-29 18:56:40';
        $this->assertEquals(true, $testOrm->validate('valTime'));
    }

    /**
     * @dataProvider providerValidation
     */
    public function testValidateText($connector, $testOrm)
    {
        $testOrm->valText = 5;
        $this->assertEquals(false, $testOrm->validate('valText'));
        $testOrm->valText = '5';
        $this->assertEquals(true, $testOrm->validate('valText'));
        $testOrm->valText = 5.98;
        $this->assertEquals(false, $testOrm->validate('valText'));
        $testOrm->valText = 'five';
        $this->assertEquals(true, $testOrm->validate('valText'));
        $testOrm->valText = false;
        $this->assertEquals(false, $testOrm->validate('valText'));
        $testOrm->valText = Array();
        $this->assertEquals(false, $testOrm->validate('valText'));
        $testOrm->valText = new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('mysql'));
        $this->assertEquals(false, $testOrm->validate('valText'));
    }

    /**
     * @dataProvider providerValidation
     */
    public function testValidateChar($connector, $testOrm)
    {
        $testOrm->valChar = 5;
        $this->assertEquals(false, $testOrm->validate('valChar'));
        $testOrm->valChar = 5.;
        $this->assertEquals(false, $testOrm->validate('valChar'));
        $testOrm->valChar = '5';
        $this->assertEquals(false, $testOrm->validate('valChar'));
        $testOrm->valChar = 5.98;
        $this->assertEquals(false, $testOrm->validate('valChar'));
        $testOrm->valChar = 'five';
        $this->assertEquals(false, $testOrm->validate('valChar'));
        $testOrm->valChar = false;
        $this->assertEquals(false, $testOrm->validate('valChar'));
        $testOrm->valChar = Array();
        $this->assertEquals(false, $testOrm->validate('valChar'));
        $testOrm->valChar = new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('mysql'));
        $this->assertEquals(false, $testOrm->validate('valChar'));

        $testOrm->valChar = 'ab';
        $this->assertEquals(false, $testOrm->validate('valChar'));
        $testOrm->valChar = 'abc';
        $this->assertEquals(true, $testOrm->validate('valChar'));
        $testOrm->valChar = 'abcd';
        $this->assertEquals(false, $testOrm->validate('valChar'));
    }

    /**
     * @dataProvider providerValidation
     */
    public function testValidateEnum($connector, $testOrm)
    {
        $testOrm->valEnum = 5;
        $this->assertEquals(false, $testOrm->validate('valEnum'));
        $testOrm->valEnum = 5.;
        $this->assertEquals(false, $testOrm->validate('valEnum'));
        $testOrm->valEnum = '5';
        $this->assertEquals(false, $testOrm->validate('valEnum'));
        $testOrm->valEnum = 5.98;
        $this->assertEquals(false, $testOrm->validate('valEnum'));
        $testOrm->valEnum = 'five';
        $this->assertEquals(false, $testOrm->validate('valEnum'));
        $testOrm->valEnum = false;
        $this->assertEquals(false, $testOrm->validate('valEnum'));
        $testOrm->valEnum = Array();
        $this->assertEquals(false, $testOrm->validate('valEnum'));
        $testOrm->valEnum = new \ObjectRelationMapper\Tests\ORMTestValidation(NULL, $this->getQueryBuilder('mysql'));
        $this->assertEquals(false, $testOrm->validate('valEnum'));

        $testOrm->valEnum = 'ab';
        $this->assertEquals(false, $testOrm->validate('valEnum'));
        $testOrm->valEnum = 'abc';
        $this->assertEquals(true, $testOrm->validate('valEnum'));
        $testOrm->valEnum = 'abcd';
        $this->assertEquals(false, $testOrm->validate('valEnum'));
        $testOrm->valEnum = 'def';
        $this->assertEquals(true, $testOrm->validate('valEnum'));
        $testOrm->valEnum = 'cba';
        $this->assertEquals(false, $testOrm->validate('valEnum'));
    }

}