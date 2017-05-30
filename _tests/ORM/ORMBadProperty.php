<?php

namespace ObjectRelationMapper\Tests;

/**
 * Class ORMTestValidation
 * @property int data
 */
class ORMBadProperty extends TestBaseClass
{
    protected function setUp()
    {
        $this->addColumn('qc_int', 'data', 'int', '10');

        $this->setConfigDbPrimaryKey('qc_int');
        $this->setConfigDbServer('master');
        $this->setConfigObject(__CLASS__);
        $this->setConfigDbTable('d_validate_types');
    }
}