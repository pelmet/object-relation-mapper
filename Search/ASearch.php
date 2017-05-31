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
	 * Vrati count query
	 * @return mixed
	 */
	public function getCountQuery()
	{
		return $this->connector->composeCountQuery();
	}

	/**
	 * Vrati count
	 * @return int
	 */
	public function getCount()
	{
		return $this->orm->countByQuery($this->connector->composeCountQuery(), $this->connector->getParams());
	}

	/**
	 * Vrati vsechny vysledky
     * @return array
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
	 * @return array
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

    public function getResultsWithChildrenLoaded()
    {
        return $this->connector->getResultsWithChildrenLoaded();
    }


    public function getResultsInArray()
    {
        return $this->connector->getResultsInArray();
    }
}
