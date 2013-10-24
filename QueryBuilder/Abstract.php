<?php

abstract class ObjectRelationMapper_QueryBuilder_Abstract
{
	/**
	 * @param ObjectRelationMapper_ORM $orm
	 * @return Array
	 */
	abstract public function load	(ObjectRelationMapper_ORM $orm);

	/**
	 * @param ObjectRelationMapper_ORM $orm
	 * @return boolean
	 */
	abstract public function insert	(ObjectRelationMapper_ORM $orm);

	/**
	 * @param ObjectRelationMapper_ORM $orm
	 * @return boolean
	 */
	abstract public function update	(ObjectRelationMapper_ORM $orm);

	/**
	 * @param ObjectRelationMapper_ORM $orm
	 * @return boolean
	 */
	abstract public function delete	(ObjectRelationMapper_ORM $orm);

	/**
	 * @param ObjectRelationMapper_ORM $orm
	 * @return int
	 */
	abstract public function count	(ObjectRelationMapper_ORM $orm);
}