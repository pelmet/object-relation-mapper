<?php


class ObjectRelationMapper_Listing_Column_Map extends ObjectRelationMapper_Listing_Column_Abstract implements ObjectRelationMapper_Listing_Column_Interface
{
	protected $map;

	/**
	 * Nazev Vstupniho sloupce
	 * @param $sourceName
	 * @param array $map
	 */
	public function __construct($sourceName, Array $map)
	{
		$this->sourceName = $sourceName;
		$this->map = $map;
	}

	/**
	 * @inheritdoc
	 */
	protected function getValue(ObjectRelationMapper_Listing_Connector_RowInterface $source)
	{
		$value = parent::getValue($source);

		if(isset($this->map[$value])){
			return $this->map[$value];
		} else {
			return $value;
		}
	}
}