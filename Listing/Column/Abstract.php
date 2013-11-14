<?php

/**
 * Class ObjectRelationMapper_Listing_Column_Abstract
 *
 * @method title
 */
abstract class ObjectRelationMapper_Listing_Column_Abstract extends ObjectRelationMapper_Listing_Column_Abstract_Style
{
	protected $source;
	protected $sourceName;


	/**
	 * Prelozi hodnotu dane veci z ORMka
	 * @param ObjectRelationMapper_Listing_Connector_RowInterface $source
	 * @return string
	 */
	public function translate(ObjectRelationMapper_Listing_Connector_RowInterface $source)
	{
		return $this->formatData($this->getValue($source));
	}

	/**
	 * Vrati hodnotu z ORM
	 * @param ObjectRelationMapper_Listing_Connector_RowInterface $source
	 * @return mixed|null
	 */
	protected function getValue(ObjectRelationMapper_Listing_Connector_RowInterface $source)
	{
		return $this->value($source, $this->sourceName);
	}

	/**
	 * @param ObjectRelationMapper_Listing_Connector_RowInterface $source
	 * @param $override
	 * @return mixed
	 */
	protected function getOverrideValue(ObjectRelationMapper_Listing_Connector_RowInterface $source, $override)
	{
		return $this->value($source, $override);
	}

	/**
	 * @param \ObjectRelationMapper_Listing_Connector_RowInterface $source
	 * @param $property
	 * @return mixed
	 */
	protected function value(ObjectRelationMapper_Listing_Connector_RowInterface $source, $property)
	{
		return $source->getValue($property);
	}

	/**
	 * Prida wrap tag
	 * @param $htmlTag
	 * @param string $attributes
	 * @return $this
	 */
	public function addWrapper($htmlTag, $attributes = '')
	{
		$this->wrappers[$htmlTag] = $attributes;
		return $this;
	}

	/**
	 * Naformatuje data
	 * @param $data
	 * @return string
	 */
	protected function formatData($data)
	{
		foreach($this->wrappers as $tag => $attribute){
			$data = sprintf('<%s %s>%s</%s>', $tag, $attribute, $data, $tag);
		}
		return $data;
	}
}