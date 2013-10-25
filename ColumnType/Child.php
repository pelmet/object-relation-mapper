<?php

class ObjectRelationMapper_ColumnType_Child extends ObjectRelationMapper_ColumnType_Abstract implements ObjectRelationMapper_ColumnType_Interface
{
	protected $ormName;
	protected $alias;
	protected $localKey;
	protected $foreignKey;
	protected $additionalParams = Array();

	/**
	 * Construct
	 * @param $ormName
	 * @param $alias
	 * @param string $localKey
	 * @param string $foreignKey
	 * @param array $additionalParams
	 */
	public function __construct($ormName, $alias, $localKey, $foreignKey, $additionalParams = Array())
	{
		$this->ormName = $ormName;
		$this->alias = $alias;
		$this->localKey = $localKey;
		$this->foreignKey = $foreignKey;
		$this->additionalParams = $additionalParams;
	}

	public function generateDbLine()
	{
		return '';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		return true;
	}
}