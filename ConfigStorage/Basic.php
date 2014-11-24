<?php

namespace ObjectRelationMapper\ConfigStorage;

class Basic extends AStorage implements IStorage
{
	protected static $fullyConfigured = Array();
	protected static $configurationStorage = Array();

	/**
	 * Nastavi Konfiguraci ORMka
	 * @param $orm
	 * @param array $basicConfiguration
	 * @param array $dbCols
	 * @param array $phpAliases
	 * @param array $childs
	 * @param array $dataAliases
	 */
	public static function setConfiguration($orm, Array $configuration)
	{
		self::$fullyConfigured[$orm] = TRUE;

		self::$configurationStorage[$orm] = $configuration;
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
		if (isset(self::$fullyConfigured[$orm]) && self::$fullyConfigured[$orm] === true) {
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