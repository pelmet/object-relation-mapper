<?php

namespace ObjectRelationMapper\ObjectRelationMapper;

interface IORM
{
	public function save($forceInsert = false);
	public function delete($deleteNow = false);
	public function load($loadData = NULL);
	public function loadByPrimaryKey();
	public function count();
	public function loadMultiple($loadData = NULL);
}