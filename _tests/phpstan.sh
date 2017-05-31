#!/bin/bash

phpstan analyze -l 1 --autoload-file=phpstan-autoload.php ./../Base ./../QueryBuilder ./../ColumnType ./../ConfigStorage ./../Connector ./../DataExchange ./../DataMiner ./../Exception ./../Search