<?php

namespace ObjectRelationMapper;

/**
 * Class Storage
 * Ulozna pro ORM konfigurace
 */
class ConfigStorage_Abstract
{
	const BASIC_CONFIG = 1;
	const DB_COLS = 2;
	const PHP_ALIASES = 3;
	const ALL_DB_FIELDS = 4;
	const ALL_DB_FIELDS_WITH_TABLE = 5;
	const ALL_ALIASES = 6;
	const CHILDS = 7;
	const DATA_ALIASES = 8;
}