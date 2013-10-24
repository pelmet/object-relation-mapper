<?php

class ObjectRelationMapper_ConfigStorage_Basic extends ObjectRelationMapper_ConfigStorage_Abstract implements ObjectRelationMapper_ConfigStorage_Interface
{
	protected static $fullyConfigured = Array();
	protected static $configurationStorage = Array();

	/**
	 * Nastavi Konfiguraci ORMka
	 * @param $orm
	 * @param array $basicConfiguration
	 * @param array $dbCols
	 * @param array $phpAliases
	 */
	public static function setConfiguration($orm, Array $basicConfiguration, Array $dbCols, Array $phpAliases)
	{
		self::$fullyConfigured[$orm] = TRUE;

		self::$configurationStorage[$orm] = Array(
			self::BASIC_CONFIG => $basicConfiguration,
			self::DB_COLS => $dbCols,
			self::PHP_ALIASES => $phpAliases
		);

		// ALL DB Fields
		foreach($dbCols as $column){
			self::$configurationStorage[$orm][self::ALL_DB_FIELDS][] = $column->col;
			self::$configurationStorage[$orm][self::ALL_DB_FIELDS_WITH_TABLE][] = $basicConfiguration['DbTable'] . '.' . $column->col;
			self::$configurationStorage[$orm][self::ALL_ALIASES][] = $column->alias;
		}
	}

	/**
	 * Vrati konfiguraci ORMka
	 * @param string $orm
	 * @return mixed
	 */
	public static function &getConfiguration($orm)
	{
		return self::$configurationStorage[$orm];
	}

	/**
	 * Vrati true/false podle toho zda existuje ulozena konfigurace
	 * @param $orm
	 * @return bool
	 */
	public static function configurationExists($orm)
	{
		if(isset(self::$fullyConfigured[$orm]) && self::$fullyConfigured[$orm] === true){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Vrati pouze urcittou cast konfigurace
	 * @param $orm
	 * @param $configType
	 * @return mixed
	 */
	public static function &getSpecificConfiguration($orm, $configType)
	{
		return self::$configurationStorage[$orm][$configType];
	}
}