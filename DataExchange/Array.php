<?php

class ObjectRelationMapper_DataExchange_Array extends ObjectRelationMapper_DataExchange_Abstract implements ObjectRelationMapper_DataExchange_Interface
{

	/**
	 * @inheritdoc
	 */
	public function load($data)
	{
		foreach($data as $property => $value){
			$this->orm->{$property} = $value;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function export()
	{
		$returnData = Array();

		foreach($this->orm as $property => $value){
			$returnData[$property] = $value;
		}

		return $returnData;
	}
}