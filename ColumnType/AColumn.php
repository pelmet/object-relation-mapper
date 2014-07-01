<?php

namespace ObjectRelationMapper\ColumnType;
use ObjectRelationMapper\Exception\ColumnType as EColumnType;

/**
 * Class AColumn
 * Definice Db Radku
 */
abstract class AColumn
{
	protected $col;
	protected $alias;
	protected $type;
	protected $length;
	protected $additionalParams = Array();

	/**
	 * Construct
	 * @param $col
	 * @param $alias
	 * @param string $type
	 * @param string $length
	 * @param array $additionalParams
	 */
	public function __construct($col, $alias, $type = 'string', $length = '255', $additionalParams = Array())
	{
		$this->col = $col;
		$this->alias = $alias;
		$this->type = $type;
		$this->length = $length;
		$this->additionalParams = $additionalParams;
	}

	/**
	 * Vrati hodnotu policka
	 * @param $propertyName
	 * @return mixed
	 * @throws ColumnType
	 */
	public function &__get($propertyName)
	{
		if(property_exists($this, $propertyName)){
			return $this->{$propertyName};
		} else {
			throw new EColumnType('Property ' . $propertyName . ' neexistuje, opravte si prosim kod.');
		}
	}
}