<?php

class ObjectRelationMapper_Listing_Connector_Search_Row implements ObjectRelationMapper_Listing_Connector_RowInterface
{
	protected $orm;

	public function __construct(ObjectRelationMapper_ORM $orm)
	{
		$this->orm = $orm;
	}

	public function getValue($property)
	{
		if(method_exists($this->orm, $property)){
			return call_user_func(Array($this->orm, $property));
		} elseif(preg_match('/^(.*?)\.(.*)$/i', $property)){
			return $this->orm->cProperty($property);
		} else {
			return $this->orm->{$property};
		}
	}
}