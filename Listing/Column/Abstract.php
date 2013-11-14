<?php

/**
 * Class ObjectRelationMapper_Listing_Column_Abstract
 *
 * @method title
 */
abstract class ObjectRelationMapper_Listing_Column_Abstract
{
	protected $source;
	protected $sourceName;

	protected $styles = Array();

	protected $attributes = Array();

	/**
	 * GET
	 * @param $property
	 * @return null
	 */
	public function __get($property)
	{
		if(property_exists($this, $property)){
			return $this->{$property};
		} else {
			return NULL;
		}
	}

	/**
	 * SET
	 * @param $property
	 * @param $value
	 */
	public function __set($property, $value)
	{
		if(property_exists($this, $property)){
			$this->{$property} = $value;
		}
	}

	/**
	 * Pridavani atributu
	 */
	public function __call($function, $arguments)
	{
		$this->attributes[$function][] = Array('style' => $arguments[0], 'condition' => (isset($arguments[1]) ? $arguments[1] : NULL));
	}

	/**
	 * Pokud se hodnota v condition rovna momentalni hodnote pridej styl bunky
	 * @param $style
	 * @param $condition
	 */
	public function style($style, $condition)
	{
		$this->styles[] = Array('condition' => $condition, 'style' => $style);
	}

	public function getAttributes(ObjectRelationMapper_ORM $source)
	{
		$return = Array();
		foreach($this->attributes as $key => $attr){
			$attribute = Array();
			foreach($attr as $style){
				if($this->getValue($source) == $style['condition']){
					if(preg_match('/value\:(.*)/i', $style['style'], $matches)){
						$attribute[] = $this->getOverrideValue($source, $matches[1]);
					} else {
						$attribute[] = $style['style'];
					}
				} elseif(is_null($style['condition'])){
					if(preg_match('/value\:(.*)/i', $style['style'], $matches)){
						$attribute[] = $this->getOverrideValue($source, $matches[1]);
					} else {
						$attribute[] = $style['style'];
					}
				}
			}
			$return[] = $key . '="' .implode(' ', $attribute).'"';
		}

		return implode(' ', $return);
	}

	/**
	 * Vrati obsah atributu style dle zadanych podminek
	 */
	public function getCellStyle(ObjectRelationMapper_ORM $source)
	{
		$return = Array();
		foreach($this->styles as $style){
			if($this->getValue($source) == $style['condition']){
				$return[] = $style['style'];
			}
		}
		return $this->getAttributes($source) . ' style="' . implode('; ', $return) . '"';
	}

	/**
	 * Obarvi bunku za danych podminek
	 * @param $colour
	 * @param $condition
	 */
	public function colorize($colour, $condition)
	{
		$this->style('background-color: '.$colour, $condition);
	}

	/**
	 * Prida barvy z pole podminka => barva
	 * @param array $data
	 */
	public function colorizeArray(Array $data)
	{
		foreach($data as $condition =>  $colour){
			$this->style('background-color: '.$colour, $condition);
		}
	}

	/**
	 * Prida Csskove tridy class => condition
	 * @param array $classes
	 */
	public function classArray(Array $classes)
	{
		foreach($classes as  $condition => $class ){
			$this->class($class, $condition);
		}
	}

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
		return $this->value($source, $this->sourceName);
	}

	/**
	 * @param ObjectRelationMapper_ORM $source
	 * @param $override
	 * @return mixed
	 */
	protected function getOverrideValue(ObjectRelationMapper_ORM $source, $override)
	{
		return $this->value($source, $override);
	}

	/**
	 * @param ObjectRelationMapper_ORM $source
	 * @param $property
	 * @return mixed
	 */
	protected function value(ObjectRelationMapper_ORM $source, $property)
	{
		if(method_exists($source, $property)){
			return call_user_func(Array($source, $property));
		} elseif(preg_match('/^(.*?)\.(.*)$/i', $property)){
			return $source->cProperty($property);
		} else {
			return $source->{$property};
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