<?php

namespace ObjectRelationMapper;

/**
 * Class ObjectRelationMapper
 *
 * @property mixed primaryKey
 */
abstract class ORM extends Common implements Base\IORM
{
	/**
	 * Da se prepsat na cokoliv jineho v extendovane tride
	 */
	protected function setORMStorages()
	{
		$this->configStorage 	= 'ConfigStorage\Basic';
		$this->queryBuilder		= new QueryBuilder\DB();
	}

	/**
	 * Nahraje objekt z daneho storage
	 * @param Array $loadData
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
	 * Vlozi najednou vice orm v jednom dotazu (vhodne pro importy, neloaduje ormka zpet)
	 * @param array $loadData
	 * @return mixed
	 * @throws Exception\ORM
	 */
	public function insertMultiple(Array $loadData)
	{
		if(empty($loadData)){
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
        if(empty($query)){
            throw new Exception\ORM('Nemohu loadovat pres prazdnou query.');
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
	 * @throws Exception\ORM
	 */
	public function countByQuery($query, $params)
	{
		if(empty($query)){
			throw new Exception\ORM('Nemohu loadovat pres prazdnou query.');
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
			$orm->setOrderingOrder($order, (is_null($direction) ? Base\AORM::ORDERING_ASCENDING : $direction));
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