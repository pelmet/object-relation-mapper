<?php

$upDir = __DIR__ . '/../';

$dirs = Array('Interface', 'ObjectRelationMapper');

foreach($dirs as $value){
    $files = scandir($upDir . $value);
    foreach($files as $file){
        require_once $upDir . $value . '/' . $file;
    }
}

