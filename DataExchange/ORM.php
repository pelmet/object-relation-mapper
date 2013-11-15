<?php

namespace ObjectRelationMapper;

class DataExchange_ORM extends DataExchange_Abstract implements DataExchange_Interface
{
	/**
	 * @inheritdoc
	 */
	public function load($data)
	{
		foreach($data as $property => $value){
			if(!isset($this->excluded[$property]) && $this->orm->propertyExists($property)){
				$this->orm->{$property} = $value;
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function export()
	{
		return $this->orm;
	}
}