<?php

namespace ObjectRelationMapper\Base;

use ObjectRelationMapper\ColumnType\IColumn;
use ObjectRelationMapper\ConfigStorage\AStorage;
use ObjectRelationMapper\ConfigStorage\IStorage;
use ObjectRelationMapper\Exception\ORM as EORM;
use ObjectRelationMapper\Generator\DbToOrm;
use ObjectRelationMapper\QueryBuilder\ABuilder;

/**
 * Class ORM_Abstract
 *
 * Obsluzne a pomocne funkce jsou rozdelene zde pro lepsi prehlednost
 *
 * @property mixed $primaryKey
 * @method setConfigDbTable($name)
 * @method setConfigDbServer($master)
 * @method setConfigObject($__CLASS__)
 * @method setConfigDbPrimaryKey($key)
 * @method primaryKeyIsChanged()
 */
abstract class AORM extends Iterator
{
	const BASE_CONFIG_DB_SERVER = 'DbServer';
	const BASE_CONFIG_DB_TABLE = 'DbTable';
	const BASE_CONFIG_DB_PK = 'DbPrimaryKey';
	const BASE_CONFIG_OBJECT = 'Object';

	const ORDERING_ASCENDING = 'ASC';
	const ORDERING_DESCENDING = 'DESC';

	protected $requiredBasicConfiguration = Array(
		self::BASE_CONFIG_DB_SERVER => TRUE,
		self::BASE_CONFIG_DB_TABLE => TRUE,
		self::BASE_CONFIG_DB_PK => TRUE,
		self::BASE_CONFIG_OBJECT => TRUE
	);
	protected $basicConfiguration = Array();

	/**
	 * Pole Sloupecku
	 * @var array
	 */
	protected $columns = Array();

	/**
	 * @var array
	 */
	protected $aliases = Array();

	/**
	 * @var array
	 */
	protected $childs = Array();

	/**
	 * @var array
	 */
	protected $childsData = Array();

	/**
	 * @var array
	 */
	protected $data = Array();

	/**
	 * @var array
	 */
	protected $dataAliases = Array();

	/**
	 * @var array
	 */
	protected $additionalConfiguration = Array();

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
	 * @var IStorage
	 */
	protected $configStorage;

	/**
	 * @var ABuilder
	 */
	protected $queryBuilder;

	/**
	 * @var boolean
	 */
	protected $isLoaded = false;

	/**
	 * @var bool
	 */
	protected $readOnly = false;

	/**
	 * @var bool
	 */
	protected $deleteMark = false;

	/**
	 * @var null
	 */
	protected $changedPrimaryKey = NULL;

	protected $additionalOrdering = Array(
		'Order' => Array(),
		'Offset' => 0,
		'Limit' => 1
	);

	protected $persistentParams = Array();

	protected $preGeneratedInfo = Array();

	public $beforeSave = Array();
	public $afterSave = Array();
	public $beforeLoad = Array();
	public $afterLoad = Array();
	public $beforeInsert = Array();
	public $afterInsert = Array();
	public $beforeDelete = Array();
	public $afterDelete = Array();
	public $beforeUpdate = Array();
	public $afterUpdate = Array();

	/**
	 * MFU Config pole
	 * @var array
	 */
	protected $mfuConfig = Array();
	protected $mfuActive = false;

	/**
	 * @var bool zda bylo volano setOrderingLimit
	 */
	protected $limitOverride = false;

	/**
	 * Nastaveni ORM Tridy
	 * @return void
     * @throws EORM
	 */
	abstract protected function setUp();

	abstract public function delete($deleteNow = false);

	abstract public function loadByPrimaryKey();

	abstract protected function setORMStorages();

	abstract public function children($child, $order = NULL, $direction = NULL, $limit = NULL, $offset = NULL);

	/**
	 * Vrati Storage
	 * @return IStorage
	 */
	public function getStorage()
	{
		return $this->configStorage;
	}

	/**
	 * Vrati QueryBuilder
	 * @return ABuilder
	 */
	public function &getQueryBuilder()
	{
		return $this->queryBuilder;
	}

	/**
	 * Construct
	 * @param int $primaryKey
	 * @throws EORM
	 */
	public function __construct($primaryKey = NULL)
	{
		$this->setORMStorages();
		if ($this->configurationCheck && (!$this->checkQueryBuilder() || !$this->checkORMConfigStorage())) {
			throw new EORM('Config Storage musi byt instance \ObjectRelationMapper\ConfigStorage\IStorage. Query Builder musi byt instance
			\ObjectRelationMapper\QueryBuilder\ABuilder.');
		}

		/** @var \ObjectRelationMapper\ConfigStorage\Basic $storage */
		$storage = & $this->configStorage;
		$finalClass = get_called_class();

		$this->persistParam('basicConfiguration');
		$this->persistParam('columns');
		$this->persistParam('aliases');
		$this->persistParam('childs');
		$this->persistParam('dataAliases');
		$this->persistParam('beforeLoad');
		$this->persistParam('beforeSave');
		$this->persistParam('beforeInsert');
		$this->persistParam('beforeDelete');
		$this->persistParam('beforeUpdate');
		$this->persistParam('afterLoad');
		$this->persistParam('afterSave');
		$this->persistParam('afterInsert');
		$this->persistParam('afterDelete');
		$this->persistParam('afterUpdate');
		$this->persistParam('preGeneratedInfo');
		$this->persistParam('mfuConfig');
		$this->persistAdditional();

		if ($storage::configurationExists($finalClass)) {
			foreach( $storage::getConfiguration($finalClass) as $config => &$value ){
				$this->$config = $value;
			}
		} else {
			$this->setUp();
			$this->translateConfig();

			if ($this->configurationCheck) {
				$this->isConfigurationOk();
			}

			$this->basicConfiguration['AliasPrimaryKey'] = $this->getAlias($this->basicConfiguration['DbPrimaryKey']);

			// Vybrani columns a dalsich drobnosti pred ulozenim
			foreach ($this->columns as $column) {
				$this->preGeneratedInfo['allDbFields'][] = $column->col;
				$this->preGeneratedInfo['allDbFieldsWithTable'][] = $this->basicConfiguration['DbTable'] . '.' . $column->col;
				$this->preGeneratedInfo['allAliases'][] = $column->alias;
			}

			// MFU Predgenerovani
			$mfuConfig = Array();
			foreach ($this->mfuConfig as $alias => $configuration){
				$currentAlias = Array();

				foreach($configuration as $column => $active){
					$currentAlias['allDbFields'][] = $this->aliases[$column]->col;
					$currentAlias['allDbFieldsWithTable'][] = $this->basicConfiguration['DbTable'] . '.' . $this->aliases[$column]->col;
				}

				$mfuConfig[$alias] = $currentAlias;
			}
			$this->mfuConfig = $mfuConfig;

			$config = Array();
			foreach ( $this->persistentParams as $persistentParam => $save ){
				$config[$persistentParam] = $this->$persistentParam;
			}

			$storage::setConfiguration($finalClass, $config);
		}

		if ($primaryKey !== NULL) {
			$this->setPrimaryKey($primaryKey);
			$this->loadByPrimaryKey();
		}
	}

	/**
	 * Destruct
	 */
	public function __destruct()
	{
		if ($this->deleteMark == true) {
			$this->delete(true);
		}
	}

	/**
	 * Nastavi Persistenci parametru, moznost volat i v setupu
	 * @param string $param
	 *
	 */
	protected function persistParam($param)
	{
		$this->persistentParams[$param] = true;
	}

	/**
	 * Metoda slouzici k extendnuti a nastaveni dalsich persistent parametru
	 */
	protected function persistAdditional() {  }


	protected function translateConfig() {	}

	/**
	 * Vrati hodnotu property nebo NULL, pokud neni k dispozici
	 * @param string $property
	 * @throws EORM
	 * @return mixed|null
	 */
	public function __get($property)
	{
		if ( isset($this->data[$property]) ) {
			return $this->data[$property];
		} elseif ($property == 'primaryKey') {
			return $this->getPrimaryKey();
		} elseif ( isset($this->childsData[$property]) ) {
			return $this->childsData[$property];
		} elseif ( isset($this->dataAliases[$property]) ) {
			if ($this->dataAliases[$property] instanceof \Closure) {
				return call_user_func($this->dataAliases[$property], $this);
			} else {
				$return = Array();
				foreach ($this->dataAliases[$property]['data'] as $prop) {
					$return[] = $this->{trim($prop)};
				}
				return implode($this->dataAliases[$property]['delimiter'], $return);
			}
		} else {
			if (!isset($this->aliases[$property]) && !isset($this->childs[$property]) && !isset($this->dataAliases[$property])) {
				throw new EORM($property . ' neni v ' . $this->getConfigObject() . ' nadefinovana.');
			}
			return NULL;
		}
	}

	public function __sleep()
	{
		return array('data', 'childsData');
	}

    /**
     * @throws EORM
     */
	public function __wakeup()
	{
		$this->__construct();
	}

	/**
	 * Nastavi hodnotu property
	 * @param string $property
	 * @param mixed $value
	 * @throws EORM
	 */
	public function __set($property, $value)
	{
		if (isset($this->aliases[$property])) {
			$this->changedVariables[$property] = true;
			if ($this->basicConfiguration['AliasPrimaryKey'] == $property && $this->changedPrimaryKey == NULL) {
				$this->changedPrimaryKey = $this->primaryKey;
			}
			$this->data[$property] = $value;
		} elseif (isset($this->childs[$property])) {
			$this->childsData[$property] = $value;
		} elseif ($property == 'primaryKey') {
			$this->setPrimaryKey($value);
		}  else {
			throw new EORM($property . ' neni v ' . $this->getConfigObject() . ' nadefinovana.');
		}
	}

	/**
	 * Vrati konfiguraci nastaeni primarniho klice
	 * @return mixed
	 */
	public function getConfigDbPrimaryKey()
	{
		return $this->basicConfiguration['DbPrimaryKey'];
	}

	public function getConfigDbTable()
	{
		return $this->basicConfiguration['DbTable'];
	}

	public function getConfigDbServer()
	{
		return $this->basicConfiguration['DbServer'];
	}

	public function getConfigObject()
	{
		return $this->basicConfiguration['Object'];
	}

	public function getOrderingLimit()
	{
		return $this->additionalOrdering['Limit'];
	}

	public function setOrderingLimit($limit)
	{
		$this->limitOverride = true;
		$this->additionalOrdering['Limit'] = $limit;
	}

	public function getOrderingOffset()
	{
		return $this->additionalOrdering['Offset'];
	}

	public function setOrderingOffset($offset)
	{
		$this->additionalOrdering['Offset'] = $offset;
	}

	/**
	 * Obecny caller pro urctite typy metod
	 * @param string $function
	 * @param array $arguments
	 * @throws EORM
	 * @return mixed
	 */
	public function __call($function, Array $arguments)
	{
		if (preg_match('/^get(.*)$/', $function, $matches)) {
			if (preg_match('/^get(.*)Config/', $function, $matches) && isset($this->aliases[lcfirst($matches[1])])) {
				return $this->aliases[lcfirst($matches[1])];
			}

			if (preg_match('/^getChild(.*)Config$/', $function, $matches) && isset($this->childs[lcfirst($matches[1])])) {
				return $this->childs[lcfirst($matches[1])];
			}

			if (preg_match('/^getFirst(.*)$/', $function, $matches) && isset($this->childs[lcfirst($matches[1])])) {
				if (!isset($this->childsData[lcfirst($matches[1])])) {
					$this->children(lcfirst($matches[1]));
				}

				if (isset($this->childsData[lcfirst($matches[1])][0])) {
					return $this->childsData[lcfirst($matches[1])][0];
				} else {
					return NULL;
				}
			}
		} elseif (preg_match('/^set(.*)$/', $function, $matches)) {
			// setConfig
			if (preg_match('/^setConfig(.*)$/', $function, $matches) && isset($this->requiredBasicConfiguration[$matches[1]])) {
				$this->basicConfiguration[$matches[1]] = $arguments[0];
				return true;
			}
		}

		if (preg_match('/^primaryKeyIsChanged$/', $function)) {
			return isset($this->changedVariables[$this->getAlias($this->getConfigDbPrimaryKey())]);
		}

		if (preg_match('/^(.*)IsChanged$/', $function, $matches) && isset($this->aliases[$matches[1]])) {
			return isset($this->changedVariables[$matches[1]]);
		}

		if (preg_match('/^(.*)Full$/i', $function, $matches) && isset($this->aliases[$matches[1]])) {
			return $this->getDbField($matches[1], true);
		}

		if (preg_match('/^(.*)$/i', $function, $matches) && isset($this->aliases[$matches[1]])) {
			return $this->getDbField($matches[1]);
		}

		throw new EORM('Dynamicka funkce s nazvem ' . $function . ' nemuze byt spustena, neni totiz definovana.');
	}

	public function generatePHPDocEssential()
    {
        $returnArray = Array();

        foreach ($this->aliases as $value) {
            $returnArray[] = ' * @property ' . DbToOrm::getPhpPropertyType(DbToOrm::getColumnPhpType($value->type)) . ' $' . $value->alias;
        }

        foreach ($this->childs as $value) {
            $returnArray[] = ' * @property ' . $value->ormName . '[] $' . $value->alias;
            $returnArray[] = ' * @method ' . $value->ormName . '|NULL getFirst' . ucfirst($value->alias) . '()';
        }

        return implode("\n", $returnArray) . "\n";
    }

    /**
     * Generate full PHPDoc With everything possible
     * @return string
     */
    public function generatePHPDocFull()
    {
        $returnArray = Array();

        foreach ($this->aliases as $value) {
            $returnArray[] = ' * @method string ' . $value->alias . '()';
            $returnArray[] = ' * @method string ' . $value->alias . 'Full()';
            $returnArray[] = ' * @method \ObjectRelationMapper\ColumnType\C' . ucfirst($value->type) . ' get' . ucfirst($value->alias) . 'Config()';
            $returnArray[] = ' * @method bool ' . $value->alias . 'IsChanged()';
        }

        foreach ($this->childs as $value) {
            $returnArray[] = ' * @method \ObjectRelationMapper\ColumnType\Child getChild' . ucfirst($value->alias) . 'Config()';
        }

        $returnArray[] = ' * @method bool primaryKeyIsChanged()';

        return $this->generatePHPDocEssential() . implode("\n", $returnArray) . "\n";
    }

    /**
     * Magic Method __isset
     * @param string $property
     * @return bool
     * @throws EORM
     */
	public function __isset($property)
	{
		if ($property == 'primaryKey') {
			return isset($this->{$this->getAlias($this->getConfigDbPrimaryKey())});
		} else {
			return isset($this->data[$property]) || isset($this->childsData[$property]);
		}
	}

    /**
     * Magic Method __empty
     * @param string $property
     * @return bool
     * @throws EORM
     */
	public function __empty($property)
	{
		if ($property == 'primaryKey') {
			return empty($this->{$this->getAlias($this->getConfigDbPrimaryKey())});
		} else {
			return empty($this->data[$property]) || empty($this->childsData[$property]);
		}
	}

	/**
	 * @inheritdoc
	 */
	protected function getIterableName()
	{
		return 'data';
	}

	/**
	 * Zkontroluje ORM Storage
	 * @return bool
	 */
	private function checkORMConfigStorage()
	{
		$cs = new $this->configStorage();
		if ($cs instanceof IStorage) {
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
		if ($this->queryBuilder instanceof ABuilder) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Nastavi primarni klic
	 * @param mixed $primaryKey
	 */
	protected function setPrimaryKey($primaryKey)
	{
		$this->{$this->basicConfiguration['AliasPrimaryKey']} = $primaryKey;
	}

	/**
	 * Nastavi primarni klic
	 * @return mixed
	 */
	protected function getPrimaryKey()
	{
		return $this->{$this->basicConfiguration['AliasPrimaryKey']};
	}

	/**
	 * Vypne kontrolu konfigurace ORMka
	 * @param bool $check
	 */
	protected function setConfigurationCheck($check = false)
	{
		$this->configurationCheck = $check;
	}

	/**
	 * Prida Sloupecek
	 * @param string $dbName
	 * @param string $phpAlias
	 * @param string $dbType
	 * @param string $length
	 * @param array $additionalParams
	 * @throws EORM
	 */
	protected function addColumn($dbName, $phpAlias, $dbType = 'string', $length = '255', $additionalParams = Array())
	{
		if(property_exists($this, $phpAlias)){
			throw new EORM('Alias se nesmi shodovat s jiz definovanou vnitrni ORM property');
		}

		$className = '\\ObjectRelationMapper\\ColumnType\\C' . ucfirst($dbType);
		if (!class_exists($className)) {
			throw new EORM('Trida ' . $className . ' neexistuje. Typ ' . $dbType . ' nelze pouzit, dokud nebude nadefinovana');
		} else {
			$col = new $className($dbName, $phpAlias, $dbType, $length, $additionalParams);

			if (!$col instanceof IColumn) {
				throw new EORM('Trida ' . $className . ' neimplementuje \\ObjectRelationMapper\\ObjectRelationMapper\\ColumnType\\IColumn. Typ ' . $dbType . ' nelze pouzit, dokud toto nebude opraveno');
			}
		}

		$this->columns[$dbName] = $col;
		$this->aliases[$phpAlias] = $col;
	}

	/**
	 * Prida childa podle propojovacich klicu
	 * @param string $ormName
	 * @param string $phpAlias
	 * @param string $localKey
	 * @param string $foreignKey
	 * @param array $additionalParams
	 * @throws EORM
	 */
	protected function addChild($ormName, $phpAlias, $localKey, $foreignKey, $additionalParams = Array())
	{
		if(property_exists($this, $phpAlias)){
			throw new EORM('Alias se nesmi shodovat s jiz definovanou vnitrni ORM property');
		}

		$className = 'ObjectRelationMapper\ColumnType\Child';
		if (!class_exists($className) || !class_exists($ormName)) {
			throw new EORM('Trida ' . $className . ' nebo ' . $ormName . ' neexistuje.');
		} else {
			$this->childs[$phpAlias] = new $className($ormName, $phpAlias, $localKey, $foreignKey, $additionalParams);

			if (!$this->childs[$phpAlias] instanceof IColumn) {
				throw new EORM('Trida ' . $className . ' neimplementuje \\ObjectRelationMapper\\ObjectRelationMapper\\ColumnType\\IColumn. Typ child nelze pouzit, dokud toto nebude opraveno');
			}
		}
	}

	/**
	 * Prida datovy alias pouze pro cteni, pokud jsou aliases ve stringu tak je potreba pouzit jako oddelovac ,
	 * @param string $name
	 * @param \Closure|String $aliases
	 * @param string $delimiter
	 * @throws EORM
	 */
	protected function addDataAlias($name, $aliases, $delimiter = ' ')
	{
		if ($aliases instanceof \Closure) {
			$this->dataAliases[$name] = $aliases;
		} else {
			if (empty(explode(',', $aliases))) {
				throw new EORM('Chybi seznam sloupecku pro vypis');
			}
			$this->dataAliases[$name] = Array('data' => array_filter(explode(',', $aliases)), 'delimiter' => $delimiter);
		}
	}

	/**
	 * Rekne, zda je orm Spravne nakonfigurovano
     * @throws EORM
	 */
	private function isConfigurationOk()
	{
		$configured = array_diff_key($this->requiredBasicConfiguration, $this->basicConfiguration);

		if (!empty($configured)) {
			throw new EORM('Nejsou nastaveny properties ' . implode(', ', array_keys($configured)) . ' nastavte prosim tyto hodnoty');
		}

		if (empty($this->columns) || empty($this->aliases)) {
			throw new EORM('Nejsou nastaveny aliases nebo columns, nastavte prosim tyto hodnoty');
		}
	}

	/**
	 * Nastavi order
	 * @param string $column
	 * @param string $direction
	 * @throws EORM
	 */
	public function setOrderingOrder($column, $direction = self::ORDERING_ASCENDING)
	{
		if (!isset($this->columns[$column]) && !isset($this->aliases[$column])) {
			throw new EORM('Sloupec nebo alias ' . $column . ' neexistuje.');
		}

		if (isset($this->columns[$column])) {
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
		if ($returnAsString) {
			$orderingString = Array();
			foreach ($this->additionalOrdering['Order'] as $order => $direction) {
				$orderingString[] = $order . ' ' . $direction;
			}
			return implode(',', $orderingString);
		} else {
			return $this->additionalOrdering['Order'];
		}
	}

	/**
	 * Vrati nazev policka dle aliasu v php
	 * @param string $fieldName
	 * @param bool $includeTableName
	 * @throws EORM
	 * @return string
	 */
	public function getDbField($fieldName, $includeTableName = false)
	{
		if (!isset($this->aliases[$fieldName])) {
			throw new EORM('Alias pro column ' . $fieldName . ' neexistuje');
		}

		if ($includeTableName) {
			return $this->getConfigDbTable() . '.' . $this->aliases[$fieldName]->col;
		} else {
			return $this->aliases[$fieldName]->col;
		}
	}

	/**
	 * Vrati PHP Alias dle nazvu sloupecku v DB
	 * @param string $fieldName
	 * @throws EORM
	 * @return string
	 */
	public function getAlias($fieldName)
	{
		if (isset($this->columns[$fieldName])) {
			return $this->columns[$fieldName]->alias;
		} else {
			throw new EORM('Db Field pro column ' . $fieldName . ' neexistuje');
		}
	}

	/**
	 * Vrati vsechna DB POLE bud v poli nebo spojene pres glue
	 * @param mixed $glue
	 * @param bool $includeTableName
	 * @param array $exclude
	 * @return string|array
	 */
	public function getAllDbFieldsInternal($glue = NULL, $includeTableName = false, Array $exclude = Array())
	{
		if($this->mfuActive === false){
			if ($includeTableName) {
				$return = $this->preGeneratedInfo['allDbFieldsWithTable'];
			} else {
				$return = $this->preGeneratedInfo['allDbFields'];
			}
		} else {
			if ($includeTableName) {
				$return = $this->mfuConfig[$this->mfuActive]['allDbFieldsWithTable'];
			} else {
				$return = $this->mfuConfig[$this->mfuActive]['allDbFields'];
			}
		}

		if (!empty($exclude)) {
			foreach ($return as $key => &$column) {
				if (in_array($column, $exclude)) {
					unset($return[$key]);
				}
			}
		}

		if ($glue != NULL) {
			return implode($glue, $return);
		} else {
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
		$s = & $this->configStorage;

		if ($glue != NULL) {
			return implode($glue, $this->preGeneratedInfo['allAliases']);
		} else {
			return $this->preGeneratedInfo['allAliases'];
		}
	}

	public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Nahraje data do tridy z pole
     * @param array $loadData
     * @return boolean
     * @throws EORM
     */
	protected function loadClassFromArray(Array $loadData)
	{
		if (!empty($loadData)) {
			foreach ($loadData as $dbField => $actualValue) {
				if (isset($this->columns[$dbField])) {
					$this->{$this->getAlias($dbField)} = $actualValue;
				}
			}
			$this->isLoaded = true;
		} else {
			$this->isLoaded = false;
		}

        return $this->isLoaded;
	}

	/**
	 * Nastavi ORM Pouze pro cteni
	 */
	public function setReadOnly()
	{
		$this->readOnly = true;
	}

	/**
	 * Rekne zda, existuje property
	 * @param string $property
	 * @return bool
	 */
	public function propertyExists($property)
	{
		if (isset($this->aliases[$property])) {
			return true;
		}

		if (isset($this->childs[$property])) {
			return true;
		}

		return false;
	}

	/**
	 * Vrati string informaci o objektu
	 */
	public function __toString()
	{
		ob_start();
		echo get_class($this) . ":\n";
		foreach ($this->getIterator() as $key => $value) {
			echo '   "' . $key . '" = ';
			var_dump($value);
		}
		return (string)ob_get_clean();
	}

	/**
	 * Nastavi parametr jako MFU Alias
	 * @param string $parameter
	 * @param string $alias
	 */
	protected function setupMFU($parameter, $alias)
	{
		$this->mfuConfig[$alias][$parameter] = true;
	}

    /**
     * Vrati data ormka primo v poli
     * @return array
     */
	public function getData()
    {
        return $this->data;
    }

}
