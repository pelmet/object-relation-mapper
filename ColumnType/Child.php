<?php

namespace ObjectRelationMapper\ColumnType;

class Child extends AColumn implements IColumn
{
	public $ormName;
	public $alias;
	public $localKey;
	public $foreignKey;
	public $additionalParams = Array();

	/**
	 * Construct
	 * @param string $ormName
	 * @param string $alias
	 * @param string $localKey
	 * @param string $foreignKey
	 * @param array $additionalParams
	 */
	public function __construct($ormName, $alias, $localKey, $foreignKey, $additionalParams = Array())
	{
	    parent::__construct(NULL, $alias, NULL, NULL, $additionalParams);
		$this->ormName = $ormName;
		$this->localKey = $localKey;
		$this->foreignKey = $foreignKey;
	}

	public function generateDbLine()
	{
		return '';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param string $value
	 * @return bool
	 */
	public function validate($value)
	{
		return true;
	}
}