<?php
/**
 * Created by PhpStorm.
 * User: Petrs
 * Date: 3.3.15
 * Time: 12:43
 */

namespace ObjectRelationMapper\Search;


class ResultProcess {
    public $orm;
    public $dbFields;
    public $size;

    /**
     * @param \ObjectRelationMapper\DataObjects|\ObjectRelationMapper\ORM $orm
     */
    public function __construct($orm)
    {
        $this->orm = $orm;
        $this->dbFields = $orm->getAllDbFields();
        $this->size = sizeof($this->dbFields);
    }
} 