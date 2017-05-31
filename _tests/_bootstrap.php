<?php

ini_set("xdebug.overload_var_dump", "off");

$GLOBALS['databases'] = Array(
    'mysql' => Array(
        'dsn' => 'mysql:host=localhost;dbname=test',
        'user' => NULL,
        'pass' => NULL,
        'type' => 'db'
    ),
    'sqlite' => Array(
        'dsn' => 'sqlite::memory:',
        'user' => NULL,
        'pass' => NULL,
        'type' => 'db'
    ),
    'yml' => Array(
        'dsn' => '/tmp/',
        'user' => NULL,
        'pass' => NULL,
        'type' => 'file'
    )
);

define('BASE_DIR', __DIR__);

require_once __DIR__ . '/CommonTestClass.php';

function autoload($className)
{
	$className = preg_replace('/^ObjectRelationMapper/', '', $className);
	$className = str_replace('\\', '/', $className);
	$className = str_replace('_', '/', $className);

	if (is_file(__DIR__ . '/../' . '/' . $className . '.php')) {
		require_once(__DIR__ . '/../' . '/' . $className . '.php');
	}

    $className = str_replace('Tests', 'ORM', $className);

    if (is_file(__DIR__ . $className . '.php')) {
        require_once(__DIR__ . $className . '.php');
    }
}

spl_autoload_register('autoload');