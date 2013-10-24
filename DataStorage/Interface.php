<?php

interface ObjectRelationMapper_DataStorage_Interface
{
	public static function clearCache();
	public static function load($orm, $primaryKey);
}