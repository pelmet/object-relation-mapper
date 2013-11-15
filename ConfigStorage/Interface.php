<?php

namespace ObjectRelationMapper;

interface ConfigStorage_Interface
{
	public static function setConfiguration($orm, Array $basicConfiguration, Array $dbRows, Array $phpAliases, Array $childs);
	public static function &getConfiguration($orm);
	public static function &getSpecificConfiguration($orm, $configType);
	public static function configurationExists($orm);
}