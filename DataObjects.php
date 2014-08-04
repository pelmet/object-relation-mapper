<?php

namespace ObjectRelationMapper;

abstract class DataObjects extends Common
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

	protected $config;

	protected $translations = Array(
		'rows' => 'columns',
		'child' => 'childs',
		'server' => 'DbServer',
		'primaryKey' => 'DbPrimaryKey',
		'tableName' => 'DbTable',
		'object' => 'Object'
	);

	public function __construct($primaryKey = NULL)
	{
		parent::__construct($primaryKey);
		$this->config = $this->basicConfiguration;
	}

	protected function translateConfig()
	{
		$this->configurationCheck = false;

		foreach ($this->config['rows'] as $row) {
			$this->addColumn($row['name'], $row['alias'], 'string', '5000');
		}

		if(isset($this->config['child'])){
			foreach ($this->config['child'] as $child) {
				$this->addChild($child['object'], $child['name'], $child[0]['localKey'], $child[0]['foreignKey'], Array('delete' => $child['delete'], 'relation' => $child['possibilities']));
			}
		}

		$this->setConfigDbServer($this->config['server']);
		$this->setConfigDbPrimaryKey($this->config['primaryKey']);
		$this->setConfigDbTable($this->config['tableName']);
		$this->setConfigObject($this->config['object']);

		foreach(array_diff_key($this->config, $this->translations) as $key => $value){
			$this->basicConfiguration[$key] = $value;
		}
	}

	public function __get($property)
	{
		if($property == 'objectData'){
			return $this->data;
		}

		return parent::__get($property);
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

		if ($this->afterInsert(true) === false) {
			return false;
		}
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

		if ($this->afterUpdate(true) === false) {
			return false;
		}
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

		if ($this->afterLoad(true) === false) {
			return false;
		}
	}

	/**
	 * Da se prepsat na cokoliv jineho v extendovane tride
	 */
	protected function setORMStorages()
	{
		$this->configStorage = 'ConfigStorage\Basic';
		$this->queryBuilder = new QueryBuilder\DB(new Connector\ESDB());
	}

	/**
	 * Vrati vsechny data objektu
	 * @return array
	 */
	public function getData()
	{
		return $this->data + $this->childsData;
	}

	/**
	 * Nastavi childa, use with caution!
	 * @param string $childName
	 * @param mixed $childValue
	 */
	public function setChild($childName, $childValue)
	{
		$this->{$childName} = $childValue;
	}

	/**
	 * Vrati konfiguracni direktivu bud jako text, nebo jako pole hodnot
	 * @param type $configDirective
	 * @return mixed
	 */
	public function config($configDirective)
	{
		if(isset($this->translations[$configDirective])){
			return $this->{'getConfig'.$this->translations[$configDirective]}();
		} else {
			return $this->basicConfiguration[$configDirective];
		}
	}

	/**
	 * Vrati konfiguracni direktivu childu bud jako text, nebo jako pole hodnot
	 * @param type $childName
	 * @param type $configDirective
	 * @return mixed
	 */
	public function &configChild($childName, $configDirective)
	{
		return $this->childs[$childName]->{$configDirective};
	}

	/**
	 * Vrati pole aliasu
	 * @return array
	 */
	public function getConfigAliases()
	{
		return $this->getAllAliases();
	}

	/**
	 * Vrati pole db fieldu
	 * @return array
	 */
	public function getConfigDbFields()
	{
		return $this->getAllDbFields();
	}

	/**
	 * Ulozi objekt do databaze nebo do jineho specifikovaneho uloziste
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

		if ($this->afterSave(true) === false) {
			return false;
		}
	}

	/**
	 * Nahraje objekt z databaze nebo z tridy Collection nebo z pameti
	 * vraci isLoaded
	 * @param bool $forceReload
	 * @param array $loadArray
	 * @param array $additionalParams
	 * @return bool
	 */
	public function load($forceReload = false, $loadArray = NULL, $additionalParams = NULL)
	{
		if ($this->beforeLoad() === false) {
			return false;
		}

		// OBJECT RELATION MAPPER COMPAT HACK -- DO NOT REMOVE
		if (is_array($forceReload)) {
			$loadArray = $forceReload;
		}

		if (!is_null($loadArray)) {
			$this->loadClassFromArray($loadArray);
		} else {
			$this->loadClassFromArray($this->queryBuilder->load($this));
		}

		$this->changedVariables = Array();

		if ($this->afterLoad(true) === false) {
			return false;
		}
	}

	/**
	 * Vytvoreni childa z predanych vysledku DB
	 * @param string $childName
	 * @param array $loadArray
	 * @return mixed
	 * @throws Exception
	 */
	public function loadChild($childName, $loadArray)
	{
		$orm = $this->childs[$childName]->ormName;
		/** @var $orm \ObjectRelationMapper\DataObjects */
		$orm = new $orm();
		$orm->load(false, $loadArray);
		$this->{$childName} = $orm;
		return $this->{$childName};
	}

	/**
	 * Nastavi pro object delete marku, nebo rovnou objekt smaze
	 * @param bool $forceDelete
	 * @return bool
	 */
	public function delete($forceDelete = false)
	{
		if ($this->beforeDelete() === false) {
			return false;
		}

		if ($forceDelete == true) {
			$this->queryBuilder->delete($this);
		} else {
			$this->deleteMark = true;
		}

		$this->changedVariables = Array();

		if ($this->afterDelete(true) === false) {
			return false;
		}
	}

	/**
	 * Vrati objekty Children nodes, podle toho jak byly specifikovany v konfiguraci<br />
	 * (napriklad spravne propojeni tabulek atp)
	 * @param bool $child
	 * @param bool $order
	 * @param bool $direction
	 * @param bool $forceReload
	 * @param bool|int $limit
	 * @param bool|int $offset
	 * @return mixed
	 */
	public function children($child = false, $order = false, $direction = false, $forceReload = false, $limit = false, $offset = false)
	{
		if(!$forceReload && isset($this->childsData[$child])){
			return $this->{$child};
		}

		$orm = $this->childs[$child]->ormName;
		$sRelation = $this->childs[$child]->additionalParams['relation'];
		/** @var $this */
		$orm = new $orm();

		if ($order !== false) {
			$orm->setOrderingOrder($order, (($direction === false) ? Base\AORM::ORDERING_ASCENDING : $direction));
		}

		if ($limit !== false) {
			$orm->setOrderingLimit($limit);
		}

		if ($offset !== false) {
			$orm->setOrderingOffset($offset);
		}

		$localKey = $this->getAlias($this->childs[$child]->localKey);
		$foreignKey = $orm->getAlias($this->childs[$child]->foreignKey);

		if (!empty($this->{$localKey})) {
			$orm->{$foreignKey} = $this->{$localKey};
			if($sRelation == 'many'){
				$this->$child = $orm->loadMultiple();
			} else {
				$orm->load();
				$this->$child = $orm;
			}
		} else {
			if($sRelation == 'many'){
				$this->$child = Array();
			} else {
				$this->$child = null;
			}
		}

		return $this->$child;
	}

	/**
	 * Slouzi pro ziskani dat pro metodu Collection::fromORM()
	 * Nepouzivat primo
	 */
	public function _fetchAll($order = null, $direction = null, $limit = null, $offset = null, $forceReload = false)
	{
		if($order != NULL){
			$this->setOrderingOrder($order, ($direction == self::ORDERING_ASCENDING) ? self::ORDERING_ASCENDING : self::ORDERING_DESCENDING);
		}

		if($limit != NULL){
			$this->setOrderingLimit($limit);
		}

		if($offset != NULL ){
			$this->setOrderingOffset($offset);
		}

		return $this->queryBuilder->loadMultiple($this);
	}

	/**
	 * Vrati tabulku kterou objekt vyuziva
	 * @return string
	 */
	public function getTable()
	{
		return $this->getConfigDbTable();
	}

	/**
	 * Vrati server, ktery trida vyuziva pro svoji funkcnost
	 * @return string
	 */
	public function getServer()
	{
		return $this->getConfigDbServer();
	}

	/**
	 * Vraci state tridy a promennou isLoaded
	 * @return boolean
	 */
	public function isLoaded()
	{
		return $this->isLoaded;
	}

	public function getAllDbFields($imploder = ',', $includeTableName = false)
	{
		return $this->getAllDbFieldsInternal($imploder, $includeTableName);
	}
}