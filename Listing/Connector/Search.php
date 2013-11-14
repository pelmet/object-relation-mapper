<?php

class ObjectRelationMapper_Listing_Connector_Search extends ObjectRelationMaper_Listing_Connector_Abstract implements ObjectRelationMapper_Listing_Connector_Interface
{
	/**
	 * @var ObjectRelationMapper_Search_Search
	 */
	protected $dataSource;

	protected $rawData = Array();

	public function __construct(ObjectRelationMapper_Search_Search $search)
	{
		$this->dataSource = $search;

		$this->rawData = $search->getResults();

		$this->parseData();
	}

	protected function parseData()
	{
		$this->translatedData = $this->rawData;
	}


}