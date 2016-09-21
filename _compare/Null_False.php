<?php

require_once '_bootstrap.php';

$null = NULL;
$false = false;
$null2 = '1';

$time1 = microtime(true);
for ($i = 0; $i < 100000; $i++) {
	if($null == NULL){
		//
	}
}
$time1 = microtime(true) - $time1;


$time2 = microtime(true);
for ($i = 0; $i < 100000; $i++) {
	if($false == false){
		//
	}
}
$time2 = microtime(true) - $time2;

$time3 = microtime(true);
for ($i = 0; $i < 100000; $i++) {
	if(is_null($null)){
		//
	}
}
$time3 = microtime(true) - $time3;

$time4 = microtime(true);
for ($i = 0; $i < 100000; $i++) {
    if(!is_null($null2)){
        //
    }
}
$time4 = microtime(true) - $time4;


echo '100000x IF == NULL trval : ' . $time1 . "\n";
echo '100000x IF == false trval : ' . $time2 . "\n";
echo '100000x iF IS_NULL trval : ' . $time3 . "\n";
echo '100000x iF negativeif trval : ' . $time4 . "\n";
//echo '100000x iF negativeif trval : ' . $time5 . "\n";