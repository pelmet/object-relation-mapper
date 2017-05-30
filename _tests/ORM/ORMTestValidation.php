<?php

namespace ObjectRelationMapper\Tests;

/**
 * Class ORMTestValidation
 * @property int id
 * @property string valString
 * @property decimal valDecimal
 * @property boolean valBoolean
 * @property string valDate
 * @property string valTime
 * @property string valText
 * @property string valChar
 * @property string valEnum
 */
class ORMTestValidation extends TestBaseClass
{
    protected function setUp()
    {
        $this->addColumn('qc_int', 'id', 'int', '10');
        $this->addColumn('qc_string', 'valString', 'string', '10');
        $this->addColumn('qc_decimal', 'valDecimal', 'decimal', '5,2');
        $this->addColumn('qc_boolean', 'valBoolean', 'boolean', '1');
        $this->addColumn('qc_date', 'valDate', 'date');
        $this->addColumn('qc_time', 'valTime', 'timestamp');
        $this->addColumn('qc_text', 'valText', 'text');
        $this->addColumn('qc_char', 'valChar', 'char', 3);
        $this->addColumn('qc_enum', 'valEnum', 'enum', ['abc','def','ghi','jkl']);

        $this->setConfigDbPrimaryKey('qc_int');
        $this->setConfigDbServer('master');
        $this->setConfigObject(__CLASS__);
        $this->setConfigDbTable('d_validate_types');
    }
}