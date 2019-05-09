<?php

namespace ObjectRelationMapper\ColumnType;

use ObjectRelationMapper\Exception\ColumnType as EColumnType;

/**
 * Class AColumn
 * Definice Db Radku
 */
abstract class AColumn
{
	public $col;
	public $alias;
	public $type;
	public $length;
	public $additionalParams;

	/**
	 * Construct
	 * @param mixed $col
	 * @param mixed $alias
	 * @param mixed $type
	 * @param mixed $length
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
	 * @param string $propertyName
	 * @throws EColumnType
	 */
	public function &__get($propertyName)
	{
		throw new EColumnType('Property ' . $propertyName . ' neexistuje, opravte si prosim kod.');
	}
}