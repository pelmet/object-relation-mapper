<?php

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