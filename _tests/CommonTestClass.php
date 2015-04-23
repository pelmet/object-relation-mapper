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
}