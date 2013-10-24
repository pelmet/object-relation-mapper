<?php

/**
 * Class ObjectRelationMapper
 *
 * @property mixed primaryKey
 */
abstract class ObjectRelationMapper_ORM extends ObjectRelationMapper_ORM_Abstract implements ArrayAccess, IteratorAggregate, ObjectRelationMapper_ORM_Interface
{
	/**
	 * Da se prepsat na cokoliv jineho v extendovane tride
	 */
	protected function setORMStorages()
	{
		$this->configStorage 	= 'ObjectRelationMapper_ConfigStorage_Basic';
		$this->queryBuilder		= new ObjectRelationMapper_QueryBuilder_DB();
	}

	/**
	 * Nahraje objekt z daneho storage
	 * @param Array $loadData
	 * @throws Exception
	 * @return boolean|mixed
	 */
	public function load(Array $loadData = Array())
	{
		if(method_exists($this, 'beforeLoad') && $this->beforeLoad() === false){
			return false;
		}

		if(!empty($loadData)){
			$this->loadClassFromArray($loadData);
		} else {
			$this->loadClassFromArray($this->queryBuilder->load($this));
		}

		if(method_exists($this, 'afterLoad') && $this->afterLoad() === false){
			return false;
		}
	}

	/**
	 * Nahraje objekt z daneho storage
	 * @throws Exception
	 * @return boolean|mixed
	 */
	public function loadByPrimaryKey()
	{
		if(!isset($this->primaryKey) || empty($this->primaryKey)){
			throw new Exception('Nelze loadnout orm dle primarniho klice, protoze primarni klic neni nastaven.');
		}

		if(method_exists($this, 'beforeLoad') && $this->beforeLoad() === false){
			return false;
		}

		$this->loadClassFromArray($this->queryBuilder->loadByPrimaryKey($this));

		if(method_exists($this, 'afterLoad') && $this->afterLoad() === false){
			return false;
		}
	}

	/**
	 * Spocita, kolik zadanych radku odpovida nastavenym properties
	 * @return int
	 */
	public function count()
	{
		return $this->queryBuilder->count($this);
	}

	/**
	 * Ulozi objekt ORMka
	 * @param bool $forceInsert
	 * @return bool
	 */
	public function save($forceInsert = false)
	{
		if(method_exists($this, 'beforeSave') && $this->beforeSave() === false){
			return false;
		}

		if($forceInsert == true || empty($this->primaryKey)){
			$this->insert();
		} else {
			$this->update();
		}

		if(method_exists($this, 'afterSave') && $this->afterSave() === false){
			return false;
		}
	}

	/**
	 * Insert Dat
	 * @return bool
	 */
	protected function insert()
	{
		if(method_exists($this, 'beforeInsert') && $this->beforeInsert() === false){
			return false;
		}

		$this->queryBuilder->insert($this);

		if(method_exists($this, 'afterInsert') && $this->afterInsert() === false){
			return false;
		}
	}

	/**
	 * Update dat dle PK
	 * @return bool
	 */
	protected function update()
	{
		if(method_exists($this, 'beforeUpdate') && $this->beforeUpdate() === false){
			return false;
		}

		$this->queryBuilder->update($this);

		if(method_exists($this, 'afterUpdate') && $this->afterUpdate() === false){
			return false;
		}
	}

	/**
	 * Smaze ORMko z uloziste
	 * @param bool $deleteNow
	 * @return bool
	 */
	public function delete($deleteNow = false)
	{
		if(method_exists($this, 'beforeDelete') && $this->beforeDelete() === false){
			return false;
		}

		if($deleteNow == true){
			$this->queryBuilder->delete($this);
		} else {
			$this->deleteMark = true;
		}

		if(method_exists($this, 'afterDelete') && $this->afterDelete() === false){
			return false;
		}
	}

	/**
	 * Udela z ORMka kolekci
	 * @param array $loadData
	 */
	public function loadMultiple(Array $loadData = Array())
	{

	}

	/**
	 * Udela z ORMka kolekci
	 * @param bool $forceInsert
	 */
	public function saveMultiple($forceInsert = false)
	{

	}


}