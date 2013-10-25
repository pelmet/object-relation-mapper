<?php

interface ObjectRelationMapper_ORM_Interface
{
	public function save($forceInsert = false);
	public function delete($deleteNow = false);
	public function load(Array $loadData = Array());
	public function loadByPrimaryKey();
	public function count();
	public function loadMultiple(Array $loadData = Array());
}