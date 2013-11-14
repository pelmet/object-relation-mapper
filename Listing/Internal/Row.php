<?php

class ObjectRelationMapper_Listing_Internal_Row extends ObjectRelationMapper_ORM_Iterator
{
	protected $columns = Array();

	/**
	 * @var ObjectRelationMapper_ORM
	 */
	protected $sourceData = NULL;

	/**
	 * Prida column s ORMkem do stacku
	 * @param ObjectRelationMapper_Listing_Column_Interface $column
	 */
	public function addColumn(ObjectRelationMapper_Listing_Column_Interface $column)
	{
		$this->columns[] = $column;
	}

	/**
	 * Nastavi source
	 * @param ObjectRelationMapper_ORM $source
	 */
	public function setSource(ObjectRelationMapper_ORM $source)
	{
		$this->sourceData = $source;
	}

	/**
	 * Vrati source
	 * @return ObjectRelationMapper_ORM
	 */
	public function getSource()
	{
		return $this->sourceData;
	}

	/**
	 * @inheritdoc
	 */
	protected function getIterableName()
	{
		return 'columns';
	}
}