<?php

namespace ObjectRelationMapper\Base;

use ObjectRelationMapper\ColumnType\IColumn;
use ObjectRelationMapper\ConfigStorage\AStorage;
use ObjectRelationMapper\ConfigStorage\IStorage;
use ObjectRelationMapper\Exception\ORM as EORM;
use ObjectRelationMapper\QueryBuilder\ABuilder;

/**
 * Class ORM_Abstract
 *
 * Obsluzne a pomocne funkce jsou rozdelene zde pro lepsi prehlednost
 *
 * @method setConfigDbTable
 * @method setConfigDbServer
 * @method setConfigObject
 * @method setConfigDbPrimaryKey
 * @method setOrderingOffset
 * @method getOrderingOffset
 * @method setOrderingLimit
 * @method getOrderingLimit
 * @method primaryKeyIsChanged
 * @method (.*)IsChanged
 * @method getIdConfig
 * @method getChildUserConfig
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
	 * @var Array
	 */
	protected $data = Array();

	/**
	 * @var Array
	 */
	protected $dataAliases = Array();

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

	/**
	 * Nastaveni ORM Tridy
	 * @return mixed
	 */
	abstract protected function setUp();

	abstract public function delete($deleteNow = false);

	abstract public function loadByPrimaryKey();

	abstract protected function setORMStorages();

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
	public function getQueryBuilder()
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

		$storage = & $this->configStorage;
		$finalClass = get_called_class();

		if ($storage::configurationExists($finalClass)) {
			$configuration = $storage::getConfiguration($finalClass);

			$this->basicConfiguration = $configuration[$storage::BASIC_CONFIG];
			$this->columns = $configuration[$storage::DB_COLS];
			$this->aliases = $configuration[$storage::PHP_ALIASES];
			$this->childs = $configuration[$storage::CHILDS];
			$this->dataAliases = $configuration[$storage::DATA_ALIASES];
		} else {
			$this->setUp();
			$this->translateConfig();

			if ($this->configurationCheck) {
				$this->isConfigurationOk();
			}

			$storage::setConfiguration($finalClass, $this->basicConfiguration, $this->columns, $this->aliases, $this->childs, $this->dataAliases);
		}

		if (!is_null($primaryKey)) {
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

	protected function translateConfig()
	{
	}

	protected function isAliasPrimaryKey($alias)
	{
		return $this->getDbField($alias) == $this->getConfigDbPrimaryKey();
	}

	/**
	 * Vratu hodnotu property nebo NULL, pokud neni k dispozici
	 * @param $property
	 * @throws EORM
	 * @return mixed|null
	 */
	public function __get($property)
	{
		if ($property == 'primaryKey') {
			return $this->getPrimaryKey();
		} else {
			if (!isset($this->aliases[$property]) && !isset($this->childs[$property]) && !isset($this->dataAliases[$property])) {
				throw new EORM($property . ' neni v ' . $this->getConfigObject() . ' nadefinovana.');
			}

			if (isset($this->data[$property])) {
				return $this->data[$property];
			} elseif (isset($this->childsData[$property])) {
				return $this->childsData[$property];
			} elseif (isset($this->dataAliases[$property])) {
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
				return NULL;
			}
		}
	}

	public function __sleep()
	{
		return array('data', 'childsData');
	}

	public function __wakeup()
	{
		$this->__construct();
	}

	/**
	 * Nastavi hodnotu property
	 * @param $property
	 * @param $value
	 * @throws EORM
	 */
	public function __set($property, $value)
	{
		if ($property == 'primaryKey') {
			$this->setPrimaryKey($value);
		} else {
			if (!isset($this->aliases[$property]) && !isset($this->childs[$property])) {
				throw new EORM($property . ' neni v ' . $this->getConfigObject() . ' nadefinovana.');
			}

			if (isset($this->aliases[$property])) {
				$this->changedVariables[$property] = true;
				if ($this->isAliasPrimaryKey($property) && is_null($this->changedPrimaryKey)) {
					$this->changedPrimaryKey = $this->primaryKey;
				}
				$this->data[$property] = $value;
			} elseif (isset($this->childs[$property])) {
				$this->childsData[$property] = $value;
			}
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

	/**
	 * Obecny caller pro urctite typy metod
	 * @param $function
	 * @param array $arguments
	 * @throws EORM
	 * @return mixed
	 */
	public function __call($function, Array $arguments)
	{
		if (preg_match('/^get(.*)$/', $function, $matches)) {
			if (preg_match('/^getOrdering(.*)$/', $function, $matches)) {
				return $this->additionalOrdering[$matches[1]];
			}
			if (preg_match('/^get(.*)Config/', $function, $matches) && isset($this->aliases[lcfirst($matches[1])])) {
				return $this->aliases[$matches[1]];
			}

			if (preg_match('/^getChild(.*)Config$/', $function, $matches) && isset($this->childs[lcfirst($matches[1])])) {
				return $this->childs[lcfirst($matches[1])];
			}
			if (preg_match('/^getOrdering(.*)$/', $function, $matches)) {
				return $this->additionalOrdering[$matches[1]];
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

			if (preg_match('/^setOrdering(.*)$/', $function, $matches)) {
				$this->additionalOrdering[$matches[1]] = $arguments[0];
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

	/**
	 * Magic Method __isset
	 * @param $property
	 * @return bool
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
	 * @param $property
	 * @return bool
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
	 * @param $primaryKey
	 */
	protected function setPrimaryKey($primaryKey)
	{
		$this->{$this->getAlias($this->getConfigDbPrimaryKey())} = $primaryKey;
	}

	/**
	 * Nastavi primarni klic
	 * @return mixed
	 */
	protected function getPrimaryKey()
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
	 * @throws EORM
	 */
	protected function addColumn($dbName, $phpAlias, $dbType = 'string', $length = '255', $additionalParams = Array())
	{
		$className = 'ObjectRelationMapper\ColumnType\\' . ucfirst($dbType);
		if (!class_exists($className)) {
			throw new EORM('Trida ' . $className . ' neexistuje. Typ ' . $dbType . ' nelze pouzit, dokud nebude nadefinovana');
		} else {
			$col = new $className($dbName, $phpAlias, $dbType, $length, $additionalParams);

			if (!$col instanceof IColumn) {
				throw new EORM('Trida ' . $className . ' neimplementuje ObjectRelationMapper\\ObjectRelationMapper\ColumnType\IColumn. Typ ' . $dbType . ' nelze pouzit, dokud toto nebude opraveno');
			}
		}

		$this->columns[$dbName] = $col;
		$this->aliases[$phpAlias] = $col;
	}

	/**
	 * Prida childa podle propojovacich klicu
	 * @param $ormName
	 * @param $phpAlias
	 * @param $localKey
	 * @param $foreignKey
	 * @param array $additionalParams
	 * @throws \ObjectRelationMapper\Exception\ORM
	 */
	protected function addChild($ormName, $phpAlias, $localKey, $foreignKey, $additionalParams = Array())
	{
		$className = 'ObjectRelationMapper\ColumnType\Child';
		if (!class_exists($className) || !class_exists($ormName)) {
			throw new EORM('Trida ' . $className . ' nebo ' . $ormName . ' neexistuje.');
		} else {
			$this->childs[$phpAlias] = new $className($ormName, $phpAlias, $localKey, $foreignKey, $additionalParams);

			if (!$this->childs[$phpAlias] instanceof IColumn) {
				throw new EORM('Trida ' . $className . ' neimplementuje ObjectRelationMapper\\ObjectRelationMapper\ColumnType\IColumn. Typ child nelze pouzit, dokud toto nebude opraveno');
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
	 * @param $column
	 * @param string $direction
	 * @throws ORM
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
	 * @param $fieldName
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
	 * @param $fieldName
	 * @throws EORM
	 * @return string
	 */
	public function getAlias($fieldName)
	{
		if (!isset($this->columns[$fieldName])) {
			throw new EORM('Db Field pro column ' . $fieldName . ' neexistuje');
		}

		return $this->columns[$fieldName]->alias;
	}

	/**
	 * Vrati vsechna DB POLE bud v poli nebo spojene pres glue
	 * @param null $glue
	 * @param bool $includeTableName
	 * @param array $exclude
	 * @return string|array
	 */
	public function getAllDbFields($glue = NULL, $includeTableName = false, Array $exclude = Array())
	{
		$s = & $this->configStorage;

		if ($includeTableName) {
			$return = $s::getSpecificConfiguration($this->getConfigObject(), AStorage::ALL_DB_FIELDS_WITH_TABLE);
		} else {
			$return = $s::getSpecificConfiguration($this->getConfigObject(), AStorage::ALL_DB_FIELDS);
		}

		if (!empty($exclude)) {
			foreach ($return as $key => &$column) {
				if (in_array($column, $exclude)) {
					unset($return[$key]);
				}
			}
		}

		if (!is_null($glue)) {
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

		if (!is_null($glue)) {
			return implode($glue, $s::getSpecificConfiguration($this->getConfigObject(), AStorage::ALL_ALIASES));
		} else {
			return $s::getSpecificConfiguration($this->getConfigObject(), AStorage::ALL_ALIASES);
		}
	}

	/**
	 * Nahraje data do tridy z pole
	 * @param array $loadData
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
	 * @param $property
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
	 * Return orm properties
	 * @param null $glue
	 * @return Array|string
	 */
	public function ormPropertyGenerator($glue = NULL)
	{
		$returnArray = Array();

		foreach ($this->aliases as $value) {
			$returnArray[] = ' * @property ' . $value->type . ' ' . $value->alias;
		}

		if (!is_null($glue)) {
			return implode($glue, $returnArray);
		} else {
			return $returnArray;
		}
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
		return ob_get_clean();
	}
}