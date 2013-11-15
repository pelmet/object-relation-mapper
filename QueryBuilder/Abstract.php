<?php

namespace ObjectRelationMapper;

abstract class QueryBuilder_Abstract
{
	/**
	 * @param ORM $orm
	 * @return Array
	 */
	abstract public function load	(ORM $orm);

	/**
	 * @param ORM $orm
	 * @return Array
	 */
	abstract public function loadMultiple	(ORM $orm);

    /**
     * @param ORM $orm
     * @param $query
     * @param $params
     * @return Array
     */
    abstract public function loadByQuery	(ORM $orm, $query, $params);

	/**
	 * @param ORM $orm
	 * @param $query
	 * @param $params
	 * @return Array
	 */
	abstract public function countByQuery	(ORM $orm, $query, $params);

	/**
	 * @param ORM $orm
	 * @return boolean
	 */
	abstract public function insert	(ORM $orm);

	/**
	 * @param ORM $orm
	 * @return boolean
	 */
	abstract public function update	(ORM $orm);

	/**
	 * @param ORM $orm
	 * @return boolean
	 */
	abstract public function delete	(ORM $orm);

	/**
	 * @param ORM $orm
	 * @return int
	 */
	abstract public function count	(ORM $orm);
}