<?php

namespace ObjectRelationMapper;

abstract class DataObjects extends Common
{
	protected $config;

	protected function translateConfig()
	{
		foreach ($this->config['rows'] as $row) {
			$this->addColumn($row['name'], $row['alias'], 'string', '5000');
		}

		foreach ($this->config['child'] as $child) {
			$this->addChild($child['object'], $child['name'], $child[0]['localKey'], $child[0]['foreignKey'], Array('delete' => $child['delete'], 'relation' => $child['possibilities']));
		}

		$this->setConfigDbServer($this->config['server']);
		$this->setConfigDbPrimaryKey($this->config['primaryKey']);
		$this->setConfigDbTable($this->config['tableName']);
		$this->setConfigObject($this->config['object']);
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
	 * Automaticky overloading pro class properties
	 * @param set|get(functionCall) $function
	 * @param Array $arguments
	 * @return mixed
	 */
	public function __call($function, Array $arguments)
	{
		if (preg_match('/get([A-Z][a-z]+)/', $function, $matches) && isset($this->childs[$matches[1]])) {
			return $this->children($matches[1]);
		}

		return parent::__call($function, $arguments);
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
	public function &config($configDirective)
	{
		return $this->config[$configDirective];
	}

	/**
	 * Vrati konfiguracni direktivu childu bud jako text, nebo jako pole hodnot
	 * @param type $childName
	 * @param type $configDirective
	 * @return mixed
	 */
	public function &configChild($childName, $configDirective)
	{
		return $this->configChildren[$childName][$configDirective];
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

		if ($this->afterSave() === false) {
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

		if ($this->afterLoad() === false) {
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
		$orm = new $orm();
		$this->{$childName} = $orm->loadMultiple($loadArray);
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

		if ($this->afterDelete() === false) {
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
		$orm = $this->childs[$child]->ormName;
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
			$collection = $orm->loadMultiple();
			$this->$child = $collection;
			return $collection;
		} else {
			$this->$child = Array();
			return Array();
		}
	}

	/**
	 * Propoji hodnoty, ktere nalezne v Tride Context a poli post
	 * s konfiguraci objektu ORM a vlozi je jako data do teto tridy pro
	 * dalsi pouziti
	 *
	 * @param array|Abstract_DataObjects|Abstract_Form $varToMerge
	 * @return boolean
	 */
	public function merge($varToMerge = NULL)
	{
		if (is_array($varToMerge)) {
			foreach ($this->config['rows'] as $value) {
				if ((!isset($value['merge']) || $value['merge'] == true) && isset($varToMerge[$value['alias']])) {
					$this->$value['alias'] = $varToMerge[$value['alias']];
				}
			}
			return true;
		} else if (is_null($varToMerge)) {
			return $this->merge(\Factory::Context()->post);
		} else if ($varToMerge instanceof \Abstract_DataObjects) {
			return $this->merge($varToMerge->getData());
		} else if ($varToMerge instanceof \Abstract_Form) {
			return $this->merge($varToMerge->getData());
		}

		return false;

	}

	/**
	 * Slouzi pro ziskani dat pro metodu Collection::fromORM()
	 * Nepouzivat primo
	 */
	public function _fetchAll($order = null, $direction = null, $limit = null, $offset = null, $forceReload = false)
	{
		$additionalParams = array(
			'order' => $order,
			'smer' => $direction,
			'limit' => $limit,
			'offset' => $offset
		);

		return $this->get(true, false, $forceReload, $additionalParams);
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
}