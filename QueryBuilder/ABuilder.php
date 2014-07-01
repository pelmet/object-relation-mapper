<?php

namespace ObjectRelationMapper\QueryBuilder;

use ObjectRelationMapper\ObjectRelationMapper\AORM;

abstract class ABuilder
{
	/**
	 * @param AORM $orm
	 * @return Array
	 */
	abstract public function load	(AORM $orm);

	/**
	 * @param AORM $orm
	 * @return Array
	 */
	abstract public function loadMultiple	(AORM $orm);

    /**
     * @param AORM $orm
     * @param $query
     * @param $params
     * @return Array
     */
    abstract public function loadByQuery	(AORM $orm, $query, $params);

	/**
	 * @param AORM $orm
	 * @param $query
	 * @param $params
	 * @return Array
	 */
	abstract public function countByQuery	(AORM $orm, $query, $params);

	/**
	 * @param AORM $orm
	 * @return boolean
	 */
	abstract public function insert	(AORM $orm);

	/**
	 * @param AORM $orm
	 * @param null $oldPrimaryKey
	 * @return boolean
	 */
	abstract public function update	(AORM $orm, $oldPrimaryKey = NULL);

	/**
	 * @param AORM $orm
	 * @return boolean
	 */
	abstract public function delete	(AORM $orm);

	/**
	 * @param AORM $orm
	 * @return boolean
	 */
	abstract public function deleteByOrm	(AORM $orm);

	/**
	 * @param AORM $orm
	 * @return int
	 */
	abstract public function count	(AORM $orm);

	/**
	 * @param AORM $orm
	 * @param Array $data
	 * @return int
	 */
	abstract public function insertMultiple	(AORM $orm, Array $data);
}