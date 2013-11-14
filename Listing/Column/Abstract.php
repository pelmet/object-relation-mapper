<?php


abstract class ObjectRelationMapper_Listing_Column_Abstract
{
	protected $source;
	protected $sourceName;

	/**
	 * @var array
	 */
	protected $wrappers = Array();

	/**
	 * Prelozi hodnotu dane veci z ORMka
	 * @param ObjectRelationMapper_ORM $source
	 * @return string
	 */
	public function translate(ObjectRelationMapper_ORM $source)
	{
		return $this->formatData($this->getValue($source));
	}

	/**
	 * Vrati hodnotu z ORM
	 * @param ObjectRelationMapper_ORM $source
	 * @return mixed|null
	 */
	protected function getValue(ObjectRelationMapper_ORM $source)
	{
		if(method_exists($source, $this->sourceName)){
			return call_user_func(Array($source, $this->sourceName));
		} else {
			return $source->{$this->sourceName};
		}
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
	 * Wrapne hodnotu do tagu
	 * @param $tag
	 * @param $attributes
	 * @param $data
	 * @return string
	 */
	protected function wrap($tag, $attributes, $data)
	{
		return sprintf('<%s %s>%s</%s>', $tag, $attributes, $data, $tag);
	}

	/**
	 * Naformatuje data
	 * @param $data
	 * @return string
	 */
	protected function formatData($data)
	{
		foreach($this->wrappers as $tag => $attribute){
			$data = $this->wrap($tag, $attribute, $data);
		}
		return $data;
	}
}