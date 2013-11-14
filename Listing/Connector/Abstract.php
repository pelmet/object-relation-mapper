<?php

abstract class ObjectRelationMaper_Listing_Connector_Abstract extends ObjectRelationMapper_ORM_Iterator
{
	protected $translatedData = Array();

	protected function getIterableName()
	{
		return 'translatedData';
	}

	protected $dataSource;

	abstract protected function parseData();



}