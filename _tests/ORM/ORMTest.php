<?php

namespace ObjectRelationMapper\Tests;

/**
 * Class ORMTest
 * @property int id
 * @property string startTime
 * @property string endTime
 * @property int status
 * @property string command
 */
class ORMTest extends TestBaseClass
{
    protected function setUp()
    {
        $this->addColumn('qc_id', 'id', 'int', '10');
        $this->addColumn('qc_time_start', 'startTime', 'int', '12');
        $this->addColumn('qc_time_end', 'endTime', 'int', '12');
        $this->addColumn('qc_status', 'status', 'int', '1');
        $this->addColumn('qc_command', 'command', 'string', '2000');

        $this->addDataAlias('statusStart', function ($orm) {
            return $orm->status . $orm->startTime;
        });
        $this->addDataAlias('startEndTime', 'startTime, endTime', ' ');

        $this->addChild('\ObjectRelationMapper\Tests\ORMTestChild', 'logs', 'qc_id', 'qc_id');

        $this->setConfigDbPrimaryKey('qc_id');
        $this->setConfigDbServer('master');
        $this->setConfigObject(__CLASS__);
        $this->setConfigDbTable('d_queued_commands');

        $this->setupMFU('id', 'test-data');
        $this->setupMFU('startTime', 'test-data');
    }
}