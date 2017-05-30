<?php

namespace ObjectRelationMapper\Tests;

/**
 * Class ORMBadChildProperty
 * @property int id
 * @property string valString
 * @property decimal valDecimal
 * @property boolean valBoolean
 */
class ORMBadChildProperty extends TestBaseClass
{
    protected function setUp()
    {
        $this->addColumn('qc_int', 'iblah', 'int', '10');

        $this->addChild('\ObjectRelationMapper\Tests\ORMTest', 'data', 'qc_id', 'qc_id');

        $this->setConfigDbPrimaryKey('qc_int');
        $this->setConfigDbServer('master');
        $this->setConfigObject(__CLASS__);
        $this->setConfigDbTable('d_validate_types');
    }
}