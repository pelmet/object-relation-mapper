<?php

namespace ObjectRelationMapper;

/**
 * Class ObjectRelationMapper
 *
 * @property mixed primaryKey
 */
abstract class ORM extends Common implements Base\IORM
{
	protected function beforeSave()	{ $this->internalCalls($this->beforeSave); }
	protected function afterSave() { $this->internalCalls($this->afterSave); }
	protected function beforeDelete() { $this->internalCalls($this->beforeDelete); }
	protected function afterDelete() { $this->internalCalls($this->afterDelete); }
	protected function beforeUpdate() { $this->internalCalls($this->beforeUpdate); }
	protected function afterUpdate() { $this->internalCalls($this->afterUpdate); }
	protected function beforeInsert() { $this->internalCalls($this->beforeInsert); }
	protected function afterInsert() { $this->internalCalls($this->afterInsert); }
	protected function beforeLoad() { $this->internalCalls($this->beforeLoad); }
	protected function afterLoad() { $this->internalCalls($this->afterLoad); }

	/**
	 * Vrati vsechna DB POLE bud v poli nebo spojene pres glue
	 * @param null $glue
	 * @param bool $includeTableName
	 * @param array $exclude
	 * @return string|array
	 */
	public function getAllDbFields($glue = NULL, $includeTableName = false, Array $exclude = Array())
	{
		return $this->getAllDbFieldsInternal($glue, $includeTableName, $exclude);
	}

	/**
	 * Insert Dat
	 * @return bool
	 */
	protected function insert()
	{
		if ($this->beforeInsert() === false) {
			return false;
		}

		$this->queryBuilder->insert($this);

		$this->changedVariables = Array();

		if ($this->afterInsert() === false) {
			return false;
		}

		return true;
	}

	/**
	 * Update dat dle PK
	 * @return bool
	 */
	protected function update()
	{
		if ($this->beforeUpdate() === false) {
			return false;
		}

		$this->queryBuilder->update($this, $this->changedPrimaryKey);

		$this->changedVariables = Array();

		if ($this->afterUpdate() === false) {
			return false;
		}

		return true;
	}

	/**
	 * Nahraje objekt z daneho storage
	 * @throws Exception\ORM
	 * @return boolean|mixed
	 */
	public function loadByPrimaryKey()
	{
		if (!isset($this->primaryKey) || empty($this->primaryKey)) {
			throw new Exception\ORM('Nelze loadnout orm dle primarniho klice, protoze primarni klic neni nastaven.');
		}

		if ($this->beforeLoad() === false) {
			return false;
		}

		$this->loadClassFromArray($this->queryBuilder->loadByPrimaryKey($this));

		$this->changedVariables = Array();

		if ($this->afterLoad() === false) {
			return false;
		}
	}

	/**
	 * Da se prepsat na cokoliv jineho v extendovane tride
	 */
	protected function setORMStorages()
	{
		$this->configStorage = 'ConfigStorage\Basic';
		$this->queryBuilder = new QueryBuilder\DB();
	}

	/**
	 * Nahraje objekt z daneho storage
	 * @param Array $loadData
	 * @return boolean|mixed
	 */
	public function load($loadData = NULL)
	{
		if ($this->beforeLoad() === false) {
			return false;
		}

		if (!is_null($loadData)) {
			$this->loadClassFromArray($loadData);
		} else {
			$this->loadClassFromArray($this->queryBuilder->load($this));
		}

		$this->changedVariables = Array();

		if ($this->afterLoad() === false) {
			return false;
		}

		return true;
	}

	/**
	 * Ulozi objekt ORMka
	 * @param bool $forceInsert
	 * @return bool
	 */
	public function save($forceInsert = false)
	{
		if ($this->readOnly == true) {
			return true;
		}

		if ($this->beforeSave() === false) {
			return false;
		}

		if ($forceInsert == true || empty($this->primaryKey)) {
			$this->insert();
		} else {
			$this->update();
		}

		$this->changedVariables = Array();

		if ($this->afterSave() === false) {
			return false;
		}

		return true;
	}

	/**
	 * Smaze ORMko z uloziste
	 * @param bool $deleteNow
	 * @return bool
	 */
	public function delete($deleteNow = false)
	{
		if ($this->beforeDelete() === false) {
			return false;
		}

		if ($deleteNow == true) {
			$this->queryBuilder->delete($this);
		} else {
			$this->deleteMark = true;
		}

		$this->changedVariables = Array();

		if ($this->afterDelete() === false) {
			return false;
		}

		return true;
	}

	/**
	 * Ihned smaze vsechna ormka podle definice z databaze
	 */
	public function deleteMultiple()
	{
		return $this->queryBuilder->deleteByOrm($this);
	}


	/**
	 * Vlozi najednou vice orm v jednom dotazu (vhodne pro importy, neloaduje ormka zpet)
	 * @param array $loadData
	 * @return mixed
	 * @throws Exception\ORM
	 */
	public function insertMultiple(Array $loadData)
	{
		if (empty($loadData)) {
			return false;
		}

		return $this->queryBuilder->insertMultiple($this, $loadData);
	}

	/**
	 * Nahraje objekt pres zadanou query, vykona ji a vrati pole objektu, podle toho kolik toho query vratila
	 * @param $query
	 * @param $params
	 * @return array
	 * @throws Exception\ORM
	 */
	public function loadByQuery($query, $params)
	{
		if (empty($query)) {
			throw new Exception\ORM('Nemohu loadovat pres prazdnou query.');
		}

		$collection = $this->queryBuilder->loadByQuery($this, $query, $params);

		$return = Array();
		$object = $this->getConfigObject();

		foreach ($collection as $singleOrm) {
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

		if ($order != NULL) {
			$orm->setOrderingOrder($order, ($direction == NULL ? Base\AORM::ORDERING_ASCENDING : $direction));
		}

		if ($limit != NULL) {
			$orm->setOrderingLimit($limit);
		}

		if ($offset != NULL) {
			$orm->setOrderingOffset($offset);
		}

		$localKey = $this->getAlias($this->childs[$child]->localKey);
		$foreignKey = $orm->getAlias($this->childs[$child]->foreignKey);

		if (!empty($this->{$localKey})) {
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