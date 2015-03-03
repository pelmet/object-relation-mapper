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
    public $aliases;
    public $size;

    /**
     * @param \ObjectRelationMapper\DataObjects|\ObjectRelationMapper\Base\AORM $orm
     */
    public function __construct($orm)
    {
        $this->orm = $orm;
        $this->aliases = $orm->getAllAliases();
        $this->size = sizeof($this->aliases);
    }
} 