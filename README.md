TESTS
=====

[![build status](https://git.xcroco.com/frameworks/orm/badges/master/build.svg)](https://git.xcroco.com/frameworks/orm/commits/master)
[![coverage report](https://git.xcroco.com/frameworks/orm/badges/master/coverage.svg)](https://git.xcroco.com/frameworks/orm/commits/master)

OBJECT RELATION MAPPER
======================

This project was created for several purposes listed below
*  Speed and low memory footprint
*  Objectize existing PHP code into distictible objects
*  Make easy integration into any project/codespace
*  Make ORM Modular by allowing to exchange any part of it

GENERATING PHP DOC
==================
```php
<?php

class HttpChecks extends BaseYaml
{
    function setUp()
    {
        $this->addColumn('hc_id', 'id', 'int', '11');
        $this->addColumn('hc_domain', 'domain', 'string', '1000');
        $this->addColumn('hc_servers', 'servers', 'array', '65536');
        $this->addColumn('hc_scheme', 'scheme', 'string', '20');
        $this->addColumn('hc_dns', 'dns', 'int', '1');
        $this->addColumn('hc_certificate', 'certificate', 'int', '1');
        $this->addColumn('hc_http', 'http', 'int', '1');
        $this->addColumn('hc_http_ok_response', 'httpOkResponse', 'array', '65536');
        $this->addColumn('hc_group', 'group', 'string', '20');

        $this->addChild('\ORM\CheckResults', 'results', 'hc_id', 'hc_id');
        $this->addChild('\ORM\Groups', 'groups', 'hc_group', 'g_id');

        $this->setConfigDbPrimaryKey('hc_id');
        $this->setConfigDbTable('HttpChecks-Test');
        $this->setConfigDbServer('master');
        $this->setConfigObject(__CLASS__);

        $this->setReadOnly();
    }
}

$gen = new \ORM\HttpChecks();
echo $gen->generatePHPDocEssential();
/*
 * @property int $id
 * @property string $domain
 * @property array $servers
 * @property string $scheme
 * @property int $dns
 * @property int $certificate
 * @property int $http
 * @property array $httpOkResponse
 * @property string $group
 * @property \ORM\CheckResults[] $results
 * @method \ORM\CheckResults|NULL getFirstResults()
 * @property \ORM\Groups[] $groups
 * @method \ORM\Groups|NULL getFirstGroups()
*/

echo $gen->generatePHPDocFull();
/**
   * @property int $id
   * @property string $domain
   * @property array $servers
   * @property string $scheme
   * @property int $dns
   * @property int $certificate
   * @property int $http
   * @property array $httpOkResponse
   * @property string $group
   * @property \ORM\CheckResults[] $results
   * @method \ORM\CheckResults|NULL getFirstResults()
   * @property \ORM\Groups[] $groups
   * @method \ORM\Groups|NULL getFirstGroups()
   * @method string id()
   * @method string idFull()
   * @method \ObjectRelationMapper\ColumnType\CInt getIdConfig()
   * @method bool idIsChanged()
   * @method string domain()
   * @method string domainFull()
   * @method \ObjectRelationMapper\ColumnType\CString getDomainConfig()
   * @method bool domainIsChanged()
   * @method string servers()
   * @method string serversFull()
   * @method \ObjectRelationMapper\ColumnType\CArray getServersConfig()
   * @method bool serversIsChanged()
   * @method string scheme()
   * @method string schemeFull()
   * @method \ObjectRelationMapper\ColumnType\CString getSchemeConfig()
   * @method bool schemeIsChanged()
   * @method string dns()
   * @method string dnsFull()
   * @method \ObjectRelationMapper\ColumnType\CInt getDnsConfig()
   * @method bool dnsIsChanged()
   * @method string certificate()
   * @method string certificateFull()
   * @method \ObjectRelationMapper\ColumnType\CInt getCertificateConfig()
   * @method bool certificateIsChanged()
   * @method string http()
   * @method string httpFull()
   * @method \ObjectRelationMapper\ColumnType\CInt getHttpConfig()
   * @method bool httpIsChanged()
   * @method string httpOkResponse()
   * @method string httpOkResponseFull()
   * @method \ObjectRelationMapper\ColumnType\CArray getHttpOkResponseConfig()
   * @method bool httpOkResponseIsChanged()
   * @method string group()
   * @method string groupFull()
   * @method \ObjectRelationMapper\ColumnType\CString getGroupConfig()
   * @method bool groupIsChanged()
   * @method \ObjectRelationMapper\ColumnType\Child getChildResultsConfig()
   * @method \ObjectRelationMapper\ColumnType\Child getChildGroupsConfig()
   * @method bool primaryKeyIsChanged()
 */
```

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

SEARCH EXAMPLES
===============
```php
<?php
// REPLACING DAO
namespace App\Search;

class OfflineCases extends \ObjectRelationMapper\Search\Search
{
    public function __construct()
    {
        parent::__construct(new \ORM\OfflineCases());
    }

    public function getOfflineCasesListWithPager()
    {
        $this->addOrdering('created', self::ORDERING_DESCENDING);
        $this->limit(20);
        $this->child('httpChecks');

        return $this->getResultsWithChildrenLoaded();
    }
    
    public function runCustomQuery()
    {
        $query = 'SELECT qc_id, qc_time_start, qc_time_end, qc_status, qc_command FROM d_queued_commands WHERE qc_id  = :qc_id';
        $params[] = [':qc_id', 5];
        return $this->orm->load($this->connector->runCustomLoadQuery($query, $params));
    }
}

// PRESENTER/MODULE
$search = new \App\Search\OfflineCases();
$this->template->offlineCases = $search->getOfflineCasesListWithPager();

```

MIGRATIONS
==========
```php
<?php
$migration = new \ObjectRelationMapper\Migration\Builder();
$migration->addORMToWatch(new \ORM\Service\Status());
$migration->addORMToWatch(new \ORM\Graphite\Check());
$migration->addORMToWatch(new \ORM\Service\Server());
$migration->addORMToWatch(new \ORM\Service\Group());
$migration->addORMToWatch(new \ORM\Service\Contact());
$migration->addORMToWatch(new \ORM\Service\OfflineCase());
if($migration->areDifferent()){
    echo $migration->getDiff();
}
```


