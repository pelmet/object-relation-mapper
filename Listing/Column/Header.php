<?php

class ObjectRelationMapper_Listing_Column_Header extends ObjectRelationMapper_Listing_Column_Abstract implements ObjectRelationMapper_Listing_Column_Interface
{
	public function __construct($name)
	{
		$this->source = $name;
	}

	public function getName()
	{
		return $this->source;
	}
}