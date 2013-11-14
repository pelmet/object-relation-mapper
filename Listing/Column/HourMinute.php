<?php


class ObjectRelationMapper_Listing_Column_HourMinute extends ObjectRelationMapper_Listing_Column_Abstract implements ObjectRelationMapper_Listing_Column_Interface
{
	/**
	 * Nazev Vstupniho sloupce
	 * @param $sourceName
	 */
	public function __construct($sourceName)
	{
		$this->sourceName = $sourceName;
	}

	/**
	 * @inheritdoc
	 */
	protected function getValue(ObjectRelationMapper_ORM $source)
	{
		$minutes = parent::getValue($source);

		if(empty($minutes)){
			return '0:00';
		} else {
			if($minutes < 0){
				$addMinus = '-';
			} elseif($minutes > 0) {
				$addMinus = '';
			} else {
				$addMinus = '';
			}

			$hours = floor(abs($minutes) / 60);
			$minutes = abs($minutes) - ($hours * 60);
			return $addMinus. ' ' .$hours . ':' . sprintf('%02d', $minutes);
		}
	}
}