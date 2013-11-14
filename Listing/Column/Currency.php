<?php


class ObjectRelationMapper_Listing_Column_Currency extends ObjectRelationMapper_Listing_Column_Abstract implements ObjectRelationMapper_Listing_Column_Interface
{
	protected $currency = '';

	/**
	 * Nazev Vstupniho sloupce
	 * @param $sourceName
	 * @param $currency
	 */
	public function __construct($sourceName, $currency)
	{
		$this->sourceName = $sourceName;
		$this->currency = $currency;
	}

	/**
	 * @inheritdoc
	 */
	protected function getValue(ObjectRelationMapper_Listing_Connector_RowInterface $source)
	{
		$value = parent::getValue($source);

		return $value . ' '. $this->currency;
	}
}