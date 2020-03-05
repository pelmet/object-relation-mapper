<?php

namespace ObjectRelationMapper\Search;

use ObjectRelationMapper\ORM;

abstract class ASearch
{
	/**
	 * @var ORM
	 */
	protected $orm;

    /**
     * @var Connector\IConnector
     */
	protected $connector;
	protected $results = Array();

    /**
     * Standardni construct
     * @param ORM $orm
     * @throws Exception\SearchException
     * @throws \ReflectionException
     */
	public function __construct(ORM $orm)
	{
		$this->orm = $orm;

		$classNameShort = (new \ReflectionClass($orm->getQueryBuilder()))->getShortName();
		$className = '\ObjectRelationMapper\Search\Connector\\'.$classNameShort;

		if(!class_exists($className)) {
		    throw new \ObjectRelationMapper\Search\Exception\SearchException('No connector available '.$classNameShort);
        }

        $this->connector = new $className($orm);
	}

	/**
	 * Vrati query
	 * @return mixed
	 */
	public function getQuery()
	{
		return $this->connector->composeLoadQuery();
	}

    /**
     * Vrati Query s parametry, vhodne spise na testovani
     * @return string
     * @internal For dev testing only
     */
    public function getQueryString()
    {
        $query = $this->connector->composeLoadQuery();
        $params = $this->connector->getParams();
        $translate = [];
        foreach ($params as $param) {
            list($key, $value) = $param;
            if (is_numeric($value)) {
                $translate[$key] = $value;
            } elseif (is_null($value)) {
                $translate[$key] = "NULL";
            } else {
                $translate[$key] = "'" . (is_string($value) ? $value : json_encode($value)) . "'";
            }
        }

        return strtr($query, $translate);
    }

	/**
	 * Vrati count query
	 * @return string
	 */
	public function getCountQuery()
	{
		return $this->connector->composeCountQuery();
	}

	/**
	 * Vrati count
	 * @return int
     * @throws \ObjectRelationMapper\Exception\ORM
	 */
	public function getCount()
	{
		return $this->orm->countByQuery($this->connector->composeCountQuery(), $this->connector->getParams());
	}

	/**
	 * Vrati vsechny vysledky
     * @return ORM[]
     */
    public function getResults()
	{
		if (empty($this->results)) {
			$queryBuilder = $this->orm->getQueryBuilder();
			$this->results = $queryBuilder->loadByQuery($this->orm, $this->connector->composeLoadQuery(), $this->connector->getParams());
		}

		return $this->orm->loadMultiple($this->results);
	}

	/**
	 * Naplni jine ORM daty z vyhledavani
	 * @param ORM $orm
	 * @return ORM[]
	 */
	public function fillDifferentORM(ORM $orm)
	{
		return $orm->loadMultiple($this->results);
	}

	/**
	 * Vyresetuje knihovnu, aby provedla dalsi vyhledavani
	 */
	public function resetSearch()
	{
		$this->results = Array();
	}

    public function addPager($pager)
    {
        $this->connector->offset($pager->getOffset());
        $this->connector->limit($pager->getLimit());
    }

    public function getResultsWithChildrenLoaded($rows = Array())
    {
        return $this->connector->getResultsWithChildrenLoaded($rows);
    }


    public function getResultsInArray()
    {
        return $this->connector->getResultsInArray();
    }
}
