<?php

namespace ObjectRelationMapper;

/**
 * Class ObjectRelationMapper
 *
 * @property mixed primaryKey
 */
abstract class ORM extends ORM_Abstract implements ORM_Interface
{
	/**
	 * Da se prepsat na cokoliv jineho v extendovane tride
	 */
	protected function setORMStorages()
	{
		$this->configStorage 	= 'ConfigStorage_Basic';
		$this->queryBuilder		= new QueryBuilder_DB();
	}

	/**
	 * Nahraje objekt z daneho storage
	 * @param Array $loadData
	 * @throws Exception
	 * @return boolean|mixed
	 */
	public function load($loadData = NULL)
	{
		if(method_exists($this, 'beforeLoad') && $this->beforeLoad() === false){
			return false;
		}

		if(!is_null($loadData)){
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

		$this->queryBuilder->update($this, $this->changedPrimaryKey);

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
	 * Ihned smaze vsechna ormka podle definice z databaze
	 */
	public function deleteMultiple()
	{
		return $this->queryBuilder->deleteByOrm($this);
	}

	/**
	 * Vrati kolekci ze zadaneho dotazu
	 * @param array $loadData
	 * @return array
	 */
	public function loadMultiple($loadData = NULL)
	{
		if($this->getOrderingLimit() == 1){
			$this->setOrderingLimit(9999999999);
		}

		if(is_null($loadData)){
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
     * Nahraje objekt pres zadanou query, vykona ji a vrati pole objektu, podle toho kolik toho query vratila
     * @param $query
     * @param $params
     * @return array
     * @throws Exception_ORM
     */
    public function loadByQuery($query, $params)
    {
        if(empty($query)){
            throw new Exception_ORM('Nemohu loadovat pres prazdnou query.');
        }

        $collection = $this->queryBuilder->loadByQuery($this, $query, $params);

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
	 * Nahraje count pres danou query
	 * @param $query
	 * @param $params
	 * @return array
	 * @throws Exception_ORM
	 */
	public function countByQuery($query, $params)
	{
		if(empty($query)){
			throw new Exception_ORM('Nemohu loadovat pres prazdnou query.');
		}

		return $this->queryBuilder->countByQuery($this, $query, $params);
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
			$orm->setOrderingOrder($order, (is_null($direction) ? ORM_Abstract::ORDERING_ASCENDING : $direction));
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

    /**
     * Vrati danou property prvniho childa ve formatu child.property[, ["$param1", "$paramx"]]
     * @example $orm->cProperty('user.name')
     * @example $orm->cProperty('user.getAllRights, [", ", "//", "adsfdsaf"]')
     * @param $string
     * @throws Exception_ORM
     * @return string
     */
    public function cProperty($string)
    {
        if(!preg_match('/^(.*)\.(.*?)(\,(.*))?$/', $string, $matches)){
            throw new Exception_ORM('Vyber child property musi byt ve formatu child.property[, ["$param1", "$paramx"]]');
        }

        if(!isset($this->childs[$matches[1]])){
            throw new Exception_ORM('Child '. $matches[1] . ' neni nadefinovan.');
        }

        if(!isset($this->childsData[$matches[1]])){
            $this->children($matches[1]);
        }

        if(isset($this->childsData[$matches[1]][0]->{$matches[2]})){
            return $this->childsData[$matches[1]][0]->{$matches[2]};
        } elseif(isset($this->childsData[$matches[1]][0]) && method_exists($this->childsData[$matches[1]][0], $matches[2])) {
	        if(isset($matches[3]) && isset($matches[4])){
		        preg_match_all('/([\'"])(.*?)([\'"])/i', $matches[4], $params);
		        return call_user_func_array(Array($this->childsData[$matches[1]][0], $matches[2]), $params[2]);
	        } else {
		        return $this->childsData[$matches[1]][0]->{$matches[2]}();
	        }

        } else {
            return NULL;
        }
    }

    /**
     * Vrati danou property vsech childu ve formatu child.property
     * @example $orm->cProperties('report.time')
     * @param $string
     * @param null $glue
     * @throws Exception_ORM
     * @return string
     */
    public function cProperties($string, $glue = NULL)
    {
        if(!preg_match('/^(.*)\.(.*)$/', $string, $matches)){
            throw new Exception_ORM('Vyber child property musi byt ve formatu child.property');
        }

        if(!isset($this->childs[$matches[1]])){
            throw new Exception_ORM('Child '. $matches[1] . ' neni nadefinovan.');
        }

        if(!isset($this->childsData[$matches[1]])){
            $this->children($matches[1]);
        }

        $return = Array();

        foreach($this->childsData[$matches[1]] as $key => $child){
            $return[$key] = $child->{$matches[2]};
        }

        if(!is_null($glue)){
            return implode($glue, $return);
        } else {
            return $return;
        }
    }
}