<?php

namespace ObjectRelationMapper\DataExchange;

class Arr extends AExchange implements IExchange
{

	/**
	 * @inheritdoc
	 */
	public function load($data)
	{
		foreach ($data as $property => $value) {
			if (!isset($this->excluded[$property]) && $this->orm->propertyExists($property) && ($value !== $this->orm->{$property})) {
				$this->orm->{$property} = $value;
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function export()
	{
		$returnData = Array();

		foreach ($this->orm as $property => $value) {
			$returnData[$property] = $value;
		}

		return $returnData;
	}
}