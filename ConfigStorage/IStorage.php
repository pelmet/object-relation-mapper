<?php

namespace ObjectRelationMapper\ConfigStorage;

interface IStorage
{
	public static function setConfiguration($orm, Array $basicConfiguration, Array $dbRows, Array $phpAliases, Array $childs, Array $dataAliases);

	public static function &getConfiguration($orm);

	public static function &getSpecificConfiguration($orm, $configType);

	public static function configurationExists($orm);
}