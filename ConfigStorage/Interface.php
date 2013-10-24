<?php

interface ObjectRelationMapper_ConfigStorage_Interface
{
	public static function setConfiguration($orm, Array $basicConfiguration, Array $dbRows, Array $phpAliases);
	public static function &getConfiguration($orm);
	public static function &getSpecificConfiguration($orm, $configType);
	public static function configurationExists($orm);
}