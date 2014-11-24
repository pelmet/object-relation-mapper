<?php

namespace ObjectRelationMapper\ConfigStorage;

interface IStorage
{
	public static function setConfiguration($orm, Array $configuration);

	public static function &getConfiguration($orm);

	public static function &getSpecificConfiguration($orm, $configType);

	public static function configurationExists($orm);
}