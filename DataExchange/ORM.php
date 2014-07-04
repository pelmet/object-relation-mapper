<?php

namespace ObjectRelationMapper\DataExchange;

class ORM extends AExchange implements IExchange
{
	/**
	 * @inheritdoc
	 */
	public function load($data)
	{
		foreach ($data as $property => $value) {
			if (!isset($this->excluded[$property]) && $this->orm->propertyExists($property)) {
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