<?php

class ObjectRelationMapper_DataStorage_PHPESDB implements ObjectRelationMapper_DataStorage_Interface
{
	protected static $dataStorage = Array();

	public static function load($orm, $primaryKey)
	{

	}

	public static function clearCache()
	{
		self::$dataStorage = Array();
	}
}