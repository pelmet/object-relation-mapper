<?php

namespace ObjectRelationMapper\Search;

/**
 * Class ColumnsOldOrm
 * @property \Abstract_DataObjects $object
 * @package ObjectRelationMapper\Search
 */
class ColumnsOldOrm extends Columns{
    public function createColumns()
    {
        $columns = array();
        foreach($this->aliases AS $alias){
            $columns[$alias] = $this->object->getDbField($alias);
        }
        $this->columns = $columns;
    }
}