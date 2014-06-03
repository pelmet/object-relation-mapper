<?php

namespace ObjectRelationMapper;

/**
 * Class ColumnType_Abstract
 * Definice Db Radku
 */
abstract class ColumnType_Abstract
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
	 * @throws Exception_ColumnType
	 */
	public function &__get($propertyName)
	{
		if(property_exists($this, $propertyName)){
			return $this->{$propertyName};
		} else {
			throw new Exception_ColumnType('Property ' . $propertyName . ' neexistuje, opravte si prosim kod.');
		}
	}

	/**
	 * metoda volajici nad typy sloupcu osetreni pro PDO
	 * pokud chceme osetrovat zmenime telo fce u potomka
	 * @param $value
	 * @return mixed
	 */
	public function getSanitezedPDOValue($value)
	{
		return $value;
	}
}