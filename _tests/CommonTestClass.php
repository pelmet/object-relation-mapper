<?php

class CommonTestClass extends PHPUnit_Framework_TestCase
{
	public function providerBasic()
	{
		return Array(
			0 => Array(new ORMTest()),
			1 => Array(new ORMTestOld())
		);
	}
	public function providerUpdateChanged()
	{
		return Array(
			0 => Array(new ORMTestUpdateFromChangedProperties()),
			1 => Array(new ORMTestUpdateFromChangedPropertiesOld())
		);
	}

	public function providerUpdateAll()
	{
		return Array(
			0 => Array(new ORMTestUpdateFromAllProperties()),
			1 => Array(new ORMTestUpdateFromAllPropertiesOld())
		);
	}
}