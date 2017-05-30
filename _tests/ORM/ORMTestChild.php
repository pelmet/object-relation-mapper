<?php

namespace ObjectRelationMapper\Tests;

/**
 * Class ORMTestChild
 * @property int id
 * @property string startTime
 * @property string endTime
 * @property int status
 * @property string command
 */
class ORMTestChild extends TestBaseClass
{
    protected function setUp()
    {
        $this->addColumn('qcl_id', 'id', 'int', '10');
        $this->addColumn('qc_id', 'queuedCommandId', 'int', '12');
        $this->addColumn('qcl_text', 'text', 'string', '2000');

        $this->addChild('\ObjectRelationMapper\Tests\ORMTest', 'command', 'qc_id', 'qc_id');

        $this->setConfigDbPrimaryKey('qcl_id');
        $this->setConfigDbServer('master');
        $this->setConfigObject(__CLASS__);
        $this->setConfigDbTable('d_queued_commands_logs');
    }
}