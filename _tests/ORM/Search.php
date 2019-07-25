<?php

namespace ObjectRelationMapper\Tests;

class Search extends \ObjectRelationMapper\Search\Search
{
    public function insertLoadQuery($query, $params, $fetchType)
    {
        return $this->connector->runCustomLoadQuery($query, $params, $fetchType);
    }
}