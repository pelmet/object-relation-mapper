#!/bin/sh

if [ ! -e ./.vendor/autoload.php ] ; then
  composer install
fi
./.bin/phpstan analyze -l 5 --autoload-file=_tests/_ps-autoload.php Base QueryBuilder ColumnType ConfigStorage Connector DataExchange DataMiner Exception Search
