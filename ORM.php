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

		$this->changedVariables = Array();

		if(method_exists($this, 'afterLoad') && $this->afterLoad() === false){
			return false;
		}
	}

	/**
	 * Nahraje objekt z daneho storage
	 * @throws Exception_ORM
	 * @return boolean|mixed
	 */
	public function loadByPrimaryKey()
	{
		if(!isset($this->primaryKey) || empty($this->primaryKey)){
			throw new Exception_ORM('Nelze loadnout orm dle primarniho klice, protoze primarni klic neni nastaven.');
		}

		if(method_exists($this, 'beforeLoad') && $this->beforeLoad() === false){
			return false;
		}

		$this->loadClassFromArray($this->queryBuilder->loadByPrimaryKey($this));

		$this->changedVariables = Array();

		if(method_exists($this, 'afterLoad') && $this->afterLoad() === false){
			return false;
		}
	}

	/**
	 * Zvaliduje vsechny hodnoty | jednu hodnotu ORMka oproti definici jeho sloupce
	 * @param null $property
	 * @return bool
	 */
	public function validate($property = NULL)
	{
		$return = true;

		if(!is_null($property)){
			$return = $this->aliases[$property]->validate($this->{$property});
		} else {
			foreach($this as $property => $value){
				if($this->aliases[$property]->validate($value) == false){
					$return = false;
					break;
				}
			}
		}

		return $return;
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
		if($this->readOnly == true){
			return true;
		}

		if(method_exists($this, 'beforeSave') && $this->beforeSave() === false){
			return false;
		}

		if($forceInsert == true || empty($this->primaryKey)){
			$this->insert();
		} else {
			$this->update();
		}

		$this->changedVariables = Array();

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

		$this->changedVariables = Array();

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

		$this->changedVariables = Array();

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

		$this->changedVariables = Array();

		if(method_exists($this, 'afterDelete') && $this->afterDelete() === false){
			return false;
		}
	}

	/**
	 * Vrati kolekci ze zadaneho dotazu
	 * @param array $loadData
	 * @return array
	 */
	public function loadMultiple(Array $loadData = Array())
	{
		if($this->getOrderingLimit() == 1){
			$this->setOrderingLimit(9999999999);
		}

		if(empty($loadData)){
			$collection = $this->queryBuilder->loadMultiple($this);
		} else {
			$collection = &$loadData;
		}

		$return = Array();
		$object = $this->getConfigObject();

		foreach($collection as $singleOrm){
			$tempOrm = new $object();
			$tempOrm->load($singleOrm);
			$return[] = $tempOrm;
		}

		return $return;
	}

	/**
	 * Vrati naloadovaneho childa a ulozi ho k pozdejsimu pouziti
	 * @param null $child
	 * @param null $order
	 * @param null $direction
	 * @param null $limit
	 * @param null $offset
	 * @return Array
	 */
	public function children($child, $order = NULL, $direction = NULL, $limit = NULL, $offset = NULL)
	{
		$orm = $this->childs[$child]->ormName;
		$orm = new $orm();

		if(!is_null($order)){
			$orm->setOrderingOrder($order, (is_null($direction) ? ObjectRelationMapper_ORM_Abstract::ORDERING_ASCENDING : $order));
		}

		if(!is_null($limit)){
			$orm->setOrderingLimit($limit);
		}

		if(!is_null($offset)){
			$orm->setOrderingOffset($offset);
		}

		$localKey = $this->getAlias($this->childs[$child]->localKey);
		$foreignKey = $orm->getAlias($this->childs[$child]->foreignKey);

		if(!empty($this->{$localKey})){
			$orm->{$foreignKey} = $this->{$localKey};
			$collection = $orm->loadMultiple();
			$this->$child = $collection;
			return $collection;
		} else {
			$this->$child = Array();
			return Array();
		}

	}
}