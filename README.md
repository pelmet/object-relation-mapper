OBJECT RELATION MAPPER
======================

This project was created for several purposes listed below
*  Speed and low memory footprint
*  Objectize existing PHP code into distictible objects
*  Make easy integration into any project/codespace
*  Make ORM Modular by allowing to exchange any part of it

SETUP
=====
ORMs by default are set only by extending base classes.

Every possible use case can be found in _tests folder, but to get you started


```php
<?php

namespace ORM;

/**
 * Class Base
 * @package ORM
 */
abstract class Base extends \ObjectRelationMapper\ORM
{
    protected function setORMStorages()
    {
        $this->configStorage = 'ObjectRelationMapper\ConfigStorage\Basic';

        $connector = new \ObjectRelationMapper\Connector\PDO(new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB, DB_USER, DB_PASS, Array(PDO::ATTR_PERSISTENT => true)));
        $this->queryBuilder = new \ObjectRelationMapper\QueryBuilder\DB($connector);
    }
}
```

```php
<?php

namespace ORM\Service;

/**
 * @property int id
 * @property string url
 * @property int status
 * @property string scheme
 * @property int lastCheck
 * @property int groupId
 **/

class Status extends \ORM\Base
{
    function setUp()
    {
        $this->addColumn('ss_id', 'id', 'int', '11');
        $this->addColumn('ss_url', 'url', 'string', '500');
        $this->addColumn('ss_status', 'status', 'int', '1');
        $this->addColumn('ss_scheme', 'scheme', 'string', '15');
        $this->addColumn('ss_last_check', 'lastCheck', 'int', '12');
        $this->addColumn('ssg_id', 'groupId', 'int', '10');

        $this->addChild('\ORM\Service\Group', 'serviceGroup', 'ssg_id', 'ssg_id');
        $this->addChild('\ORM\Service\Server', 'serviceServer', 'ss_id', 'ss_id');

        $this->setConfigDbPrimaryKey('ss_id');
        $this->setConfigDbTable('service_status');
        $this->setConfigDbServer('master');
        $this->setConfigObject(__CLASS__);
    }
}
```


