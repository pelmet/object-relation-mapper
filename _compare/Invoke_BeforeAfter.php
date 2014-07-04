<?php

require_once '_bootstrap.php';

$invoke = Array();
$invoke[] = function () use ($this) {
	return true;
};

ob_start();
$time1 = microtime(true);
for ($i = 0; $i < 100000; $i++) {
	foreach ($invoke as $method) {
		$method->__invoke();
	}
}
$time1 = microtime(true) - $time1;
ob_end_clean();


class TestClass
{
	public function run()
	{
		if (method_exists($this, 'beforeLoad') && $this->beforeLoad() !== false) {
			return true;
		}
	}
}

class TestClass2 extends TestClass
{
	protected function beforeLoad()
	{
		return true;
	}
}


$time2 = microtime(true);
$testClass = new TestClass2();
for ($i = 0; $i < 100000; $i++) {
	$testClass->run();
}
$time2 = microtime(true) - $time2;


echo '100000x invoke trval : ' . $time1 . "\n";
echo '100000x before trval : ' . $time2 . "\n";