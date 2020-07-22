<?php

namespace ObjectRelationMapper\Tests;

/**
 * Class ORMTestChildFogs
 * @property int id
 * @property string startTime
 * @property string endTime
 * @property int status
 * @property string command
 */
class ORMTestChildFogs extends TestBaseClass
{
    protected function setUp()
    {
        $this->addColumn('qcf_id', 'id', 'int', '10');
        $this->addColumn('qc_id_1', 'queuedCommandId1', 'int', '12');
        $this->addColumn('qc_id_2', 'queuedCommandId2', 'int', '12');
        $this->addColumn('qcf_text', 'text', 'string', '2000');

        $this->addChild('\ObjectRelationMapper\Tests\ORMTest', 'queuedCommand1', 'qc_id_1', 'qc_id');
        $this->addChild('\ObjectRelationMapper\Tests\ORMTest', 'queuedCommand2', 'qc_id_2', 'qc_id');

        $this->setConfigDbPrimaryKey('qcf_id');
        $this->setConfigDbServer('master');
        $this->setConfigObject(__CLASS__);
        $this->setConfigDbTable('d_queued_commands_fogs');
    }
}
