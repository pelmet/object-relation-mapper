<?php

if(is_file("/.dockerenv")) {
    $GLOBALS['databases'] = Array(
        'mariadb55' => Array(
            'dsn' => 'mysql:host=mariadb55;dbname=db_test_db',
            'user' => "dbtestuser",
            'pass' => "testpass",
            'type' => 'db',
            'test_data' => 'mysql'
        ),
        'mariadb100' => Array(
            'dsn' => 'mysql:host=mariadb100;dbname=db_test_db',
            'user' => "dbtestuser",
            'pass' => "testpass",
            'type' => 'db',
            'test_data' => 'mysql'
        ),
        'mariadb101' => Array(
            'dsn' => 'mysql:host=mariadb101;dbname=db_test_db',
            'user' => "dbtestuser",
            'pass' => "testpass",
            'type' => 'db',
            'test_data' => 'mysql'
        ),
        'mariadb102' => Array(
            'dsn' => 'mysql:host=mariadb102;dbname=db_test_db',
            'user' => "dbtestuser",
            'pass' => "testpass",
            'type' => 'db',
            'test_data' => 'mysql'
        ),
        'mariadb103' => Array(
            'dsn' => 'mysql:host=mariadb103;dbname=db_test_db',
            'user' => "dbtestuser",
            'pass' => "testpass",
            'type' => 'db',
            'test_data' => 'mysql'
        ),
        'sqlite' => Array(
            'dsn' => 'sqlite::memory:',
            'user' => NULL,
            'pass' => NULL,
            'type' => 'db',
            'test_data' => 'sqlite'
        ),
        'yml' => Array(
            'dsn' => '/tmp/',
            'user' => NULL,
            'pass' => NULL,
            'type' => 'file',
            'test_data' => 'yml'
        )
    );
} elseif (getenv('GITLAB_CI') !== false) {
    $GLOBALS['databases'] = Array(
        'mysql' => Array(
            'dsn' => 'mysql:host=localhost;dbname=test',
            'user' => NULL,
            'pass' => NULL,
            'type' => 'db',
            'test_data' => 'mysql'
        ),
        'sqlite' => Array(
            'dsn' => 'sqlite::memory:',
            'user' => NULL,
            'pass' => NULL,
            'type' => 'db',
            'test_data' => 'sqlite'
        ),
        'yml' => Array(
            'dsn' => '/tmp/',
            'user' => NULL,
            'pass' => NULL,
            'type' => 'file',
            'test_data' => 'yml'
        )
    );
} else {
    $GLOBALS['databases'] = Array(
        'mysql' => Array(
            'dsn' => 'mysql:host=localhost;dbname=test',
            'user' => 'root',
            'pass' => 'celer4000',
            'type' => 'db',
            'test_data' => 'mysql'
        ),
        'sqlite' => Array(
            'dsn' => 'sqlite::memory:',
            'user' => NULL,
            'pass' => NULL,
            'type' => 'db',
            'test_data' => 'sqlite'
        ),
        'yml' => Array(
            'dsn' => '/tmp/',
            'user' => NULL,
            'pass' => NULL,
            'type' => 'file',
            'test_data' => 'yml'
        )
    );
}


define('BASE_DIR', __DIR__);

require_once __DIR__ . '/CommonTestClass.php';
require_once __DIR__ . '/_autoload.php';

