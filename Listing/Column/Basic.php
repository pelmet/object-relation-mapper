<?php


class ObjectRelationMapper_Listing_Column_Basic extends ObjectRelationMapper_Listing_Column_Abstract implements ObjectRelationMapper_Listing_Column_Interface
{
	/**
	 * Nazev Vstupniho sloupce
	 * @param $sourceName
	 */
	public function __construct($sourceName)
	{
		$this->sourceName = $sourceName;
	}
}