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
	 * @throws ObjectRelationMapper_Exception_ORM
	 * @return boolean|mixed
	 */
	public function loadByPrimaryKey()
	{
		if(!isset($this->primaryKey) || empty($this->primaryKey)){
			throw new ObjectRelationMapper_Exception_ORM('Nelze loadnout orm dle primarniho klice, protoze primarni klic neni nastaven.');
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

    /**
     * Vrati danou property prvniho childa ve formatu child.property
     * @example $orm->cProperty('user.name')
     * @param $string
     * @throws ObjectRelationMapper_Exception_ORM
     * @return string
     */
    public function cProperty($string)
    {
        if(!preg_match('/^(.*)\.(.*)$/', $string, $matches)){
            throw new ObjectRelationMapper_Exception_ORM('Vyber child property musi byt ve formatu child.property');
        }

        if(!isset($this->childs[$matches[1]])){
            throw new ObjectRelationMapper_Exception_ORM('Child '. $matches[1] . ' neni nadefinovan.');
        }

        if(!isset($this->childsData[$matches[1]])){
            $this->children($matches[1]);
        }

        if(isset($this->childsData[$matches[1]][0]->{$matches[2]})){
            return $this->childsData[$matches[1]][0]->{$matches[2]};
        } else {
            return NULL;
        }
    }

    /**
     * Vrati danou property vsech childu ve formatu child.property
     * @example $orm->cProperties('report.time')
     * @param $string
     * @param null $glue
     * @throws ObjectRelationMapper_Exception_ORM
     * @return string
     */
    public function cProperties($string, $glue = NULL)
    {
        if(!preg_match('/^(.*)\.(.*)$/', $string, $matches)){
            throw new ObjectRelationMapper_Exception_ORM('Vyber child property musi byt ve formatu child.property');
        }

        if(!isset($this->childs[$matches[1]])){
            throw new ObjectRelationMapper_Exception_ORM('Child '. $matches[1] . ' neni nadefinovan.');
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