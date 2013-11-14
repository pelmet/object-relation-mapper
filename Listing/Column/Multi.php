<?php


class ObjectRelationMapper_Listing_Column_Multi extends ObjectRelationMapper_Listing_Column_Abstract implements ObjectRelationMapper_Listing_Column_Interface
{
	protected $delimiter;

	protected $columns = Array();

	/**
	 * Nazev Vstupniho sloupce
	 * @param $delimiter
	 */
	public function __construct($delimiter = ' ')
	{
		$this->delimiter = $delimiter;
	}

	/**
	 * Prida Vnitrek jako dalsi column
	 * @param ObjectRelationMapper_Listing_Column_Interface $column
	 */
	public function addColumn(ObjectRelationMapper_Listing_Column_Interface $column)
	{
		$this->columns[] = $column;
	}

	/**
	 * Translate hodnoty v sloupci
	 * @param ObjectRelationMapper_ORM $source
	 * @return string
	 */
	protected function getValue(ObjectRelationMapper_ORM $source)
	{
		$result = Array();

		foreach($this->columns as $column){
			$result[] = $column->translate($source);
		}

		return implode($this->delimiter, $result);
	}
}