#!/bin/bash

phpstan analyze -l 5 --autoload-file=_ps-autoload.php ./../Base ./../QueryBuilder ./../ColumnType ./../ConfigStorage ./../Connector ./../DataExchange ./../DataMiner ./../Exception ./../Search