<?php

define('PATH_ES', realpath(__DIR__ . '/../../') . '/');

// tenhle config nema bejt ani nejdrsnejsi ani nejhustsi, ma proste plnit svou predepsanou funkci, include trid aby
// tyto tridy sly testovat

setlocale(LC_ALL, 'cs_CZ.UTF-8', 'en_US.UTF-8'); // nastaveni locale pro PHP
date_default_timezone_set('Europe/Prague'); // nastaveni defaultniho casu pro funke date etc.

error_reporting(E_ALL & E_DEPRECATED);
ini_set('display_errors', '1');

$pathWeb = __DIR__ ;

require_once(PATH_ES . 'Configuration/Config.php');

// classAutoloader se muze neomezene pretezovat podle momentalni potreby, kolik cest nastavite, tolik se bude loadovat,
// aby nebylo zbytecne omezeni
Config::classAutoloader(str_ireplace('', '', __DIR__)); // cesta do slozky se souborem _config (ma byt umisten v /Configuration s tridama na webu navic autoloading vsech trid
// cesty
Config::set('webRoutePath', __DIR__); // Path, kde je ulozen route.xml
Config::set('webBasePath', str_ireplace('_es/Configuration', '', __DIR__)); // Path, do rootu webu
Config::set('webTempPath', Config::get('webBasePath') . '_tmp/'); // Path, do _tmp
Config::set('webDataPath', Config::get('webBasePath') . 'www/data/'); // Path, do data
Config::set('webWebPath', $pathWeb);

// mysql DB
define('DB_HOST', 'localhost');
define('DB_USER', 'ormtestuser');
define('DB_PASS', 'testpass');
define('DB_DB'  , 'orm_test_db');

Config::addDbLogin(DB_HOST, DB_USER, DB_PASS, DB_DB, 'mysql', 'master'); // pridani db serveru

Config::set('dbDefaultServer', 'master'); // defaultni Db server
Config::set('defaultEncodingDb', 'utf8'); // Defaultni encoding Db
Config::set('dbDefaultFetch', Db::FETCH_ASSOC); // BOTH, ASSOC, NUM
Config::set('dbDefaultForceReload', true); // zda pri kazdem dotazu nacitat data znova z Db
Config::set('dbDefaultNoCache', true); // Vyuzivat v db dotazech cache false = vyuzivat, true = nevyuzivat
Config::setDbNosqlCache('Rc'); // jakou nosql db ma db pouzivat ke cachovani dotazu?
// MC
Config::addMcLogin('localhost', '11211'); // pridani Mc loginu
Config::set('mcServerPrefix', '_'); // jaky prefix maji pouzivat vsechny zaznamy ulozene zde?
Config::useMcServer(false); // ma aplikace vyuzivat pristup k Mc serveru?
// RC
Config::addRcLogin('localhost', '6379'); // pridani redis loginu
Config::set('rcServerPrefix', '_'); // jaky prefix maji pouzivat vsechny zaznamy ulozene zde?
Config::useRcServer(false); // ma aplikace vyuzivat pristup k redis serveru?
// FC
Config::addFcPath(Config::get('webTempPath')); // pridani file Path cesty
Config::set('fcServerPrefix', '_'); // jaky prefix maji pouzivat vsechny zaznamy ulozene zde?
Config::useFcServer(false); // ma aplikace vyuzivat pristup k File Cache?
// Nastaveni Cache pro cachovani stranek
Config::setPageNosqlCache('Fc'); // jaky server se ma pouzivat na cachovani stranek?
Config::usePageNosqlCache(false); // smi se cachovat stranky?

Config::set('develEnvironment', true); // Vypisovat errory? nebo prejit do silent modu (development/ostry)

// LANG nastaveni
Config::addLanguage('cz', __DIR__.'/../Lang/cz.xml'); // prida do jazykoveho pole dalsi jazyky
Config::set('currentLanguage', 'cz'); // nastavi AKTUALNI JAZYK, da se zmenit kdykoliv do te doby, nez se poprve zavola Lang::get!


if (isset($_SERVER['HTTP_HOST'])) { // neni nastaven v parserech generovalo to obcas chyby
	Config::set('webDomain', 'http://'.$_SERVER['HTTP_HOST'].'/'); // nastaveni domeny, kterou web povazuje za vychozi bod
	Config::set('secondDomain', 'http://'.$_SERVER['HTTP_HOST'].'/');
}
Config::set('emailFromEmail', 'noreply@nobody.com'); // nastaveni emailu OD
Config::set('emailFromName', 'Default instance'); // Nastaveni jmena odesilatele

$ses = Factory::Session();

/**
 * Class ORMTest
 * @property int id
 * @property string startTime
 * @property string endTime
 * @property int status
 * @property string command
 */
class ORMTest extends ObjectRelationMapper_ORM
{
	function setUp()
	{
		$this->addColumn('qc_id', 'id', 'int', '10');
		$this->addColumn('qc_time_start', 'startTime', 'int', '12');
		$this->addColumn('qc_time_end', 'endTime', 'int', '12');
		$this->addColumn('qc_status', 'status', 'int', '1');
		$this->addColumn('qc_command', 'command', 'string', '2000');

		$this->setConfigDbPrimaryKey	('qc_id');
		$this->setConfigDbServer		('master');
		$this->setConfigObject			(__CLASS__);
		$this->setConfigDbTable			('d_queued_commands');
	}
}


