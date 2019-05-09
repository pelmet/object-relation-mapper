#!/bin/bash

phpstan analyze -l 4 --autoload-file=_ps-autoload.php -c ./../phpstan.neon ./../Base ./../QueryBuilder ./../ColumnType ./../ConfigStorage ./../Connector ./../DataExchange ./../DataMiner ./../Exception ./../Search