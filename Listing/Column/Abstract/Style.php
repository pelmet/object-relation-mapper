<?php

abstract class ObjectRelationMapper_Listing_Column_Abstract_Style
{
	protected $styles = Array();

	protected $attributes = Array();

	abstract protected function getValue(ObjectRelationMapper_Listing_Connector_RowInterface $source);
	abstract protected function getOverrideValue(ObjectRelationMapper_Listing_Connector_RowInterface $source, $override);

	/**
	 * @var array
	 */
	protected $wrappers = Array();

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

	/**
	 * Vrati vsechny atributy na vypis
	 * @param ObjectRelationMapper_Listing_Connector_RowInterface $source
	 * @return string
	 */
	public function getAttributes(ObjectRelationMapper_Listing_Connector_RowInterface $source)
	{
		$return = Array();
		foreach($this->attributes as $key => $attr){
			$attribute = Array();
			foreach($attr as $style){
				if(($this->getValue($source) == $style['condition']) || is_null($style['condition'])){
					$attribute[] = $this->getAttributeRealValue($source, $style['style']);
				}
			}
			$return[] = $key . '="' .$this->joinAttributeParts($attribute).'"';
		}

		return $this->joinAttributeParts($return);
	}

	/**
	 * Spoji atributy do pole
	 * @param array $values
	 * @param string $glue
	 * @return string
	 */
	protected function joinAttributeParts(Array $values, $glue = ' ')
	{
		return implode($glue, $values);
	}

	/**
	 * Vrati hodnotu atributu s kontrolou, zda nechceme nejakou hodnotu z radku
	 * @param ObjectRelationMapper_Listing_Connector_RowInterface $source
	 * @param $value
	 * @return mixed
	 */
	protected function getAttributeRealValue(ObjectRelationMapper_Listing_Connector_RowInterface $source, $value)
	{
		if(preg_match('/value\:(.*)/i', $value, $matches)){
			return $this->getOverrideValue($source, $matches[1]);
		} else {
			return $value;
		}
	}

	/**
	 * Vrati obsah atributu style dle zadanych podminek
	 */
	public function getCellStyle(ObjectRelationMapper_Listing_Connector_RowInterface $source)
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
}