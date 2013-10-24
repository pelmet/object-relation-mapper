<?php

/**
 * Class ObjectRelationMapper
 *
 * @method getConfigDbTable
 * @method getConfigDbServer
 * @method getConfigObject
 * @method getConfigDbPrimaryKey
 * @method setConfigDbTable
 * @method setConfigDbServer
 * @method setConfigObject
 * @method setConfigDbPrimaryKey
 * @method setOrderingOffset
 * @method getOrderingOffset
 * @method setOrderingLimit
 * @method getOrderingLimit
 *
 * @property mixed primaryKey
 */
abstract class ObjectRelationMapper_ORM implements ArrayAccess, IteratorAggregate, Countable
{
	const BASE_CONFIG_DB_SERVER = 'DbServer';
	const BASE_CONFIG_DB_TABLE 	= 'DbTable';
	const BASE_CONFIG_DB_PK 	= 'DbPrimaryKey';
	const BASE_CONFIG_OBJECT 	= 'Object';

	const ORDERING_ASCENDING 	= 'ASC';
	const ORDERING_DESCENDING 	= 'DESC';

	protected $requiredBasicConfiguration = Array(
		self::BASE_CONFIG_DB_SERVER 	=> TRUE,
		self::BASE_CONFIG_DB_TABLE 		=> TRUE,
		self::BASE_CONFIG_DB_PK		 	=> TRUE,
		self::BASE_CONFIG_OBJECT		=> TRUE
	);
	protected $basicConfiguration = Array();

	/**
	 * Pole Sloupecku
	 * @var array
	 */
	protected $columns = Array();
	protected $aliases = Array();

	/**
	 * @var Array
	 */
	protected $data;


	/**
	 * Promenne, s kterymy se nejak hybalo
	 * @var array
	 */
	protected $changedVariables = Array();

	/**
	 * Kontrolovat konfiguraci
	 * @var bool
	 */
	protected $configurationCheck = true;

	/**
	 * @var ObjectRelationMapper_ConfigStorage_Interface
	 */
	protected $configStorage;

	/**
	 * @var ObjectRelationMapper_QueryBuilder_DB
	 */
	protected $queryBuilder;

	/**
	 * @var boolean
	 */
	protected $isLoaded = false;

	/**
	 * @var bool
	 */
	protected $deleteMark = false;

	protected $additionalOrdering = Array(
		'Order' => Array(),
		'Offset' => 0,
		'Limit' => 1
	);

	/**
	 * Construct
	 * @param int $primaryKey
	 * @throws Exception
	 */
	public function __construct($primaryKey = NULL)
	{
		$this->setORMStorages();
		if($this->configurationCheck && (!$this->checkQueryBuilder() || !$this->checkORMConfigStorage())){
			throw new Exception('Config Storage musi byt instance ObjectRelationMapper_ConfigStorage_Interface. Query Builder musi byt instance
			ObjectRelationMapper_QueryBuilder_Abstract.');
		}

		$storage = &$this->configStorage;
		$finalClass = get_called_class();

		if($storage::configurationExists($finalClass)){
			$configuration = $storage::getConfiguration($finalClass);

			$this->basicConfiguration = $configuration[$storage::BASIC_CONFIG];
			$this->columns = $configuration[$storage::DB_COLS];
			$this->aliases = $configuration[$storage::PHP_ALIASES];
		} else {
			$this->setUp();

			if($this->configurationCheck){
				$this->isConfigurationOk();
			}

			$storage::setConfiguration($finalClass, $this->basicConfiguration, $this->columns, $this->aliases);
		}

		if(!is_null($primaryKey)){
			$this->setPrimaryKey($primaryKey);
			$this->loadByPrimaryKey();
		}
	}

	/**
	 * Destruct
	 */
	public function __destruct()
	{
		if($this->deleteMark == true){
			$this->delete(true);
		}
	}

	/**
	 * Vratu hodnotu property nebo NULL, pokud neni k dispozici
	 * @param $property
	 * @return mixed|null
	 */
	public function __get($property)
	{
		if($property == 'primaryKey'){
			return $this->getPrimaryKey();
		} else {
			if(isset($this->data[$property])){
				return $this->data[$property];
			} else {
				return NULL;
			}
		}
	}

	/**
	 * Nastavi hodnotu property
	 * @param $property
	 * @param $value
	 */
	public function __set($property, $value)
	{
		if($property == 'primaryKey'){
			$this->setPrimaryKey($value);
		} else {
			$this->changedVariables[$property] = true;
			$this->data[$property] = $value;
		}
	}

	/**
	 * Obecny caller pro urctite typy metod
	 * @param $function
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($function, Array $arguments)
	{
		// getConfig
		if(preg_match('/^getConfig(.*)$/', $function, $matches) && isset($this->requiredBasicConfiguration[$matches[1]])){
			return $this->basicConfiguration[$matches[1]];
		}

		// setConfig
		if(preg_match('/^setConfig(.*)$/', $function, $matches) && isset($this->requiredBasicConfiguration[$matches[1]])){
			$this->basicConfiguration[$matches[1]] = $arguments[0];
		}

		if(preg_match('/^setOrdering(.*)$/', $function, $matches)){
			$this->additionalOrdering[$matches[1]] = $arguments[0];
		}

		if(preg_match('/^getOrdering(.*)$/', $function, $matches)){
			return $this->additionalOrdering[$matches[1]];
		}
	}

	/**
	 * Magic Method __isset
	 * @param $property
	 * @return bool
	 */
	public function __isset($property)
	{
		if($property == 'primaryKey'){
			return isset($this->{$this->getAlias($this->getConfigDbPrimaryKey())});
		} else {
			return isset($this->data[$property]);
		}
	}

	/**
	 * Magic Method __empty
	 * @param $property
	 * @return bool
	 */
	public function __empty($property)
	{
		if($property == 'primaryKey'){
			return empty($this->{$this->getAlias($this->getConfigDbPrimaryKey())});
		} else {
			return empty($this->data[$property]);
		}
	}

	abstract protected function setUp();

	/**
	 * ArrayAccess implemetace
	 * @param string $offset
	 * @param mixed $value
	 */
	public final function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->data[] = $value;
		} else {
			$this->data[$offset] = $value;
		}
	}

	/**
	 * ArrayAccess implemetace
	 * @param string $offset
	 * @return mixed
	 */
	public final function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	/**
	 * ArrayAccess implemetace
	 * @param string $offset
	 */
	public final function offsetUnset($offset) {
		unset($this->data[$offset]);
	}

	/**
	 * ArrayAccess implemetace
	 * @param string $offset
	 * @return mixed
	 */
	public final function offsetGet($offset) {
		if (isset($this->data[$offset])) {
			return $this->data[$offset];
		} else {
			return null;
		}
	}

	/**
	 * Implementace IteratorAggregate
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator($this->data);
	}

	/**
	 * Implementace Countable
	 * @return int
	 */
	public final function count()
	{
		return count($this->data);
	}

	/**
	 * Da se prepsat na cokoliv jineho v extendovane tride
	 */
	protected function setORMStorages()
	{
		$this->configStorage 	= 'ObjectRelationMapper_ConfigStorage';
		$this->queryBuilder		= new ObjectRelationMapper_QueryBuilder_DB();
	}

	/**
	 * Zkontroluje ORM Storage
	 * @return bool
	 */
	private function checkORMConfigStorage()
	{
		$cs = new $this->configStorage();
		if($cs instanceof ObjectRelationMapper_ConfigStorage_Interface){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Zkontroluje ORM Storage
	 * @return bool
	 */
	private function checkQueryBuilder()
	{
		if($this->queryBuilder instanceof ObjectRelationMapper_QueryBuilder_Abstract){
			return true;
		} else {
			return false;
		}
	}



	/**
	 * Nastavi primarni klic
	 * @param $primaryKey
	 */
	private function setPrimaryKey($primaryKey)
	{
		$this->{$this->getAlias($this->getConfigDbPrimaryKey())} = $primaryKey;
	}

	/**
	 * Nastavi primarni klic
	 * @return mixed
	 */
	private function getPrimaryKey()
	{
		return $this->{$this->getAlias($this->getConfigDbPrimaryKey())};
	}

	/**
	 * Vypne kontrolu konfigurace ORMka
	 * @param $check boolean
	 */
	protected function setConfigurationCheck($check = false)
	{
		$this->configurationCheck = $check;
	}

	/**
	 * Prida Sloupecek
	 * @param $dbName
	 * @param $phpAlias
	 * @param string $dbType
	 * @param string $length
	 * @param array $additionalParams
	 * @throws Exception
	 */
	protected function addColumn($dbName, $phpAlias, $dbType = 'string', $length = '255', $additionalParams = Array())
	{
		$className = 'ObjectRelationMapper_ColumnType_' . ucfirst($dbType);
		if(!class_exists($className)){
			throw new Exception('Trida ' . $className . ' neexistuje. Typ '.$dbType. ' nelze pouzit, dokud nebude nadefinovana');
		} else {
			$col = new $className($dbName, $phpAlias, $dbType, $length, $additionalParams);

			if(!$col instanceof ObjectRelationMapper_ColumnType_Interface){
				throw new Exception('Trida ' . $className . ' neimplementuje ObjectRelationMapper_ColumnType_Interface. Typ '.$dbType. ' nelze pouzit, dokud toto nebude opraveno');
			}
		}

		$this->columns[$dbName] = $col;
		$this->aliases[$phpAlias] = $col;
	}

	/**
	 * Rekne, zda je orm Spravne nakonfigurovano
	 */
	private function isConfigurationOk()
	{
		$configured = array_diff_key($this->requiredBasicConfiguration, $this->basicConfiguration);

		if(!empty($configured)){
			throw new Exception('Nejsou nastaveny properties '. implode(', ', array_keys($configured)) . ' nastavte prosim tyto hodnoty');
		}

		if(empty($this->columns) || empty($this->aliases)){
			throw new Exception('Nejsou nastaveny aliases nebo columns, nastavte prosim tyto hodnoty');
		}
	}

	/**
	 * Vrati objekt jako pole
	 * @return Array
	 */
	public function toArray()
	{
		return $this->data;
	}



	/**
	 * Nastavi order
	 * @param $column
	 * @param string $direction
	 * @throws Exception
	 */
	public function setOrderingOrder($column, $direction = self::ORDERING_ASCENDING)
	{
		if(!isset($this->columns[$column]) && !isset($this->aliases[$column])){
			throw new Exception('Sloupec nebo alias '. $column .' neexistuje.');
		}

		if(isset($this->columns[$column])){
			$col = $this->columns[$column]->col;
		} else {
			$col = $this->aliases[$column]->col;
		}

		$this->additionalOrdering['Order'][$col] = $direction;
	}

	/**
	 * Vrati ordering podle nastaveni
	 * @param bool $returnAsString
	 * @return mixed
	 */
	public function getOrderingOrder($returnAsString = true)
	{
		if($returnAsString){
			$orderingString = Array();
			foreach($this->additionalOrdering['Order'] as $order => $direction){
				$orderingString[] = $order . ' ' . $direction;
			}
			return implode(',', $orderingString);
		} else {
			return $this->additionalOrdering['Order'];
		}
	}

	/**
	 * Vrati nazev policka dle aliasu v php
	 * @param $fieldName
	 * @param bool $includeTableName
	 * @throws Exception
	 * @return string
	 */
	public function getDbField($fieldName, $includeTableName = false)
	{
		if(!isset($this->aliases[$fieldName])){
			throw new Exception('Alias pro column '.$fieldName . ' neexistuje');
		}

		if($includeTableName){
			return $this->getConfigDbTable() . '.' . $this->aliases[$fieldName]->col;
		} else {
			return $this->aliases[$fieldName]->col;
		}
	}

	/**
	 * Vrati PHP Alias dle nazvu sloupecku v DB
	 * @param $fieldName
	 * @throws Exception
	 * @return string
	 */
	public function getAlias($fieldName)
	{
		if(!isset($this->columns[$fieldName])){
			throw new Exception('Db Field pro column '.$fieldName . ' neexistuje');
		}

		return $this->columns[$fieldName]->alias;
	}

	/**
	 * Vrati vsechna DB POLE bud v poli nebo spojene pres glue
	 * @param null $glue
	 * @param bool $includeTableName
	 * @return string|array
	 */
	public function getAllDbFields($glue = NULL, $includeTableName = false)
	{
		$s = &$this->configStorage;

		if($includeTableName){
			$return = $s::getSpecificConfiguration($this->getConfigObject(), ObjectRelationMapper_Storage::ALL_DB_FIELDS_WITH_TABLE);
		} else {
			$return = $s::getSpecificConfiguration($this->getConfigObject(), ObjectRelationMapper_Storage::ALL_DB_FIELDS);
		}

		if(!is_null($glue)){
			return implode($glue, $return);
		}  else {
			return $return;
		}
	}

	/**
	 * Odpovi, zda je ORM naloadovane
	 * @return bool
	 */
	public function isLoaded()
	{
		return $this->isLoaded;
	}

	/**
	 * Vrati vsechny aliasy bud v poli nebo spojene pres glue
	 * @param null $glue
	 * @return string|array
	 */
	public function getAllAliases($glue = NULL)
	{
		$s = &$this->configStorage;

		if(!is_null($glue)){
			return implode($glue, $s::getSpecificConfiguration($this->setConfigObject(), ObjectRelationMapper_Storage::ALL_ALIASES));
		}  else {
			return  $s::getSpecificConfiguration($this->setConfigObject(), ObjectRelationMapper_Storage::ALL_ALIASES);
		}
	}

	/**
	 * Nahraje data do tridy z pole
	 * @param array $loadData
	 * @throws Exception
	 */
	private function loadClassFromArray(Array $loadData)
	{
		if(!empty($loadData)){
			foreach($loadData as $dbField => $actualValue){
				if(isset($this->columns[$dbField])){
					$this->{$this->getAlias($dbField)} = $actualValue;
				} else {
					throw new Exception('Column '.$dbField . ' neni nadefinovan a nemuze byt nastaven');
				}
			}
			$this->isLoaded = true;
		} else {
			$this->isLoaded = false;
		}
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
	public function countRows()
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
	 */
	public function delete($deleteNow = false)
	{
		if(method_exists($this, 'beforeDelete') && $this->beforeDelete() === false){
			return false;
		}

		$this->queryBuilder->delete($this);

		if(method_exists($this, 'afterDelete') && $this->afterDelete() === false){
			return false;
		}
	}


}