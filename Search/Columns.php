<?php

namespace ObjectRelationMapper\Search;

use ObjectRelationMapper\Base\AORM;

/**
 * Class Columns
 * @property string|null $name
 * @property \ORM\Base $object
 * @property boolean $primary
 * @property Array $columns
 * @property Array $fields
 * @property Array $aliases
 * @package ObjectRelationMapper\Search
 */
class Columns {
    public $name;
    public $object = null;
    public $primary = false;
    protected $columns = array();
    public $fields = array();
    public $aliases = array();

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function getColumns()
    {
        if(empty($this->columns)){
            $this->createColumns();
        }
        return $this->columns;
    }

    public function createColumns()
    {
        $columns = array();
        $i=0;
        foreach($this->aliases AS $alias){
            $columns[$alias] = $this->fields[$i];
            $i++;
        }
        $this->columns = $columns;
    }

    public function loadData(){
        $this->fields = $this->object->getAllDbFields();
        $this->aliases = $this->object->getAllAliases();
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}