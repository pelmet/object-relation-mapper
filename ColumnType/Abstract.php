<?php

/**
 * Class ObjectRelationMapper_DBRow
 * Definice Db Radku
 */
abstract class ObjectRelationMapper_ColumnType_Abstract
{
	protected $col;
	protected $alias;
	protected $type;
	protected $length;
	protected $additionalParams = Array();

	public function __construct($col, $alias, $type = 'string', $length = '255', $additionalParams = Array())
	{
		$this->col = $col;
		$this->alias = $alias;
		$this->type = $type;
		$this->length = $length;
		$this->additionalParams = $additionalParams;
	}

	public function &__get($propertyName)
	{
		if(property_exists($this, $propertyName)){
			return $this->{$propertyName};
		} else {
			throw new Exception('Property ' . $propertyName . ' neexistuje, opravte si prosim kod.');
		}
	}
}