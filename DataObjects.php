<?php

namespace ObjectRelationMapper;

abstract class DataObjects extends ObjectRelationMapper\AORM
{
    protected $config;

    protected function translateConfig()
    {
        foreach($this->config['rows'] as $row){
            $this->addColumn($row['name'], $row['alias'], 'string', '5000');
        }

        foreach($this->config['child'] as $child){
            $this->addChild($child['object'], $child['name'], $child[0]['localKey'], $child[0]['foreignKey']);
        }

        $this->setConfigDbServer($this->config['server']);
        $this->setConfigDbPrimaryKey($this->config['primaryKey']);
        $this->setConfigDbTable($this->config['tableName']);
        $this->setConfigObject($this->config['object']);
    }

    /**
     * Da se prepsat na cokoliv jineho v extendovane tride
     */
    protected function setORMStorages()
    {
        $this->configStorage 	= 'ConfigStorage\Basic';
        $this->queryBuilder		= new QueryBuilder\DB(new Connector\ESDB());
    }

    /**
     * Automaticky overloading pro class properties
     * @param set|get(functionCall) $function
     * @param string $arguments
     * @return mixed
     */
    public function __call($function, $arguments)
    {
        if (preg_match('/get([A-Z][a-z]+)/', $function, $matches)) {
            return $this->children($matches[1]);
        }

        parent::__call($function, $arguments);
    }

    /**
     * Vrati vsechny data objektu
     * @return array
     */
    public final function getData()
    {
        return $this->data + $this->childsData;
    }

    /**
     * Nastavi childa, use with caution!
     * @param string $childName
     * @param mixed $childValue
     * @throws Exception
     */
    public function setChild($childName, $childValue)
    {
        $this->{$childName} = $childValue;
    }

    /**
     * Vrati konfiguracni direktivu bud jako text, nebo jako pole hodnot
     * @param type $configDirective
     * @return mixed
     */
    public function &config($configDirective)
    {
        return $this->config[$configDirective];
    }

    /**
     * Vrati konfiguracni direktivu childu bud jako text, nebo jako pole hodnot
     * @param type $childName
     * @param type $configDirective
     * @return mixed
     */
    public function &configChild($childName, $configDirective)
    {
        return $this->configChildren[$childName][$configDirective];
    }

    /**
     * Vrati pole aliasu
     * @return array
     */
    public function &getConfigAliases()
    {
        return $this->getAllAliases();
    }

    /**
     * Vrati pole db fieldu
     * @return array
     */
    public function &getConfigDbFields()
    {
        return $this->getAllDbFields();
    }

    /**
     * Ulozi objekt do databaze nebo do jineho specifikovaneho uloziste
     */
    public function save()
    {

    }

    /**
     * Nahraje objekt z databaze nebo z tridy Collection nebo z pameti
     * vraci isLoaded
     * @param bool $forceReload
     * @param array $loadArray
     * @param array $additionalParams
     * @return bool
     */
    public function load($forceReload = false, $loadArray = NULL, $additionalParams = NULL)
    {

    }

    /**
     * Vytvoreni childa z predanych vysledku DB
     * @param string $childName
     * @param array $loadArray
     * @return bool
     * @throws Exception
     */
    public function loadChild($childName, $loadArray)
    {

    }

    /**
     * Nastavi pro object delete marku, nebo rovnou objekt smaze
     * @param bool $forceDelete
     */
    public function delete($forceDelete = false)
    {

    }

    /**
     * Vrati objekty Children nodes, podle toho jak byly specifikovany v konfiguraci<br />
     * (napriklad spravne propojeni tabulek atp)
     * @param bool $child
     * @param bool $order
     * @param bool $direction
     * @param bool $forceReload
     * @param bool|int $limit
     * @param bool|int $offset
     * @return mixed
     */
    public function children($child = false, $order = false, $direction = false, $forceReload = false, $limit = false, $offset = false)
    {
        $childName = strtolower($child);
        if (isset($this->objectData[$childName]) && !$forceReload) {
            return $this->objectData[$childName];
        }

        if (isset($order) && $order != false) {
            $addParams['order'] = $order;
        }

        if (isset($direction) && $direction != false) {
            $addParams['smer'] = $direction;
        }

        if (isset($limit) && $limit != false) {
            $addParams['limit'] = $limit;
        }

        if (isset($offset) && $offset != false) {
            $addParams['offset'] = $offset;
        }

        if (!isset($addParams) && empty($addParams)) {
            $addParams = false;
        }

        if ($child === false) { // chceme vybrat vsechny childNodes a vsechny objekty chceme vratit zpet
            foreach ($this->configChildren as $childName => $childConfig) {
                $this->objectData[$childName] = $this->get(true, $childName, $forceReload, $addParams);
            }
        } else { // chceme vybrat jenom urciteho childa podle jmena
            $this->objectData[$childName] = $this->get(true, $childName, $forceReload, $addParams);
            return $this->objectData[$childName];
        }
    }

    /**
     * Propoji hodnoty, ktere nalezne v Tride Context a poli post
     * s konfiguraci objektu ORM a vlozi je jako data do teto tridy pro
     * dalsi pouziti
     *
     * @param array|Abstract_DataObjects|Abstract_Form $varToMerge
     * @return boolean
     */
    public function merge($varToMerge = NULL)
    {
        if (is_array($varToMerge)) {
            foreach($this->config['rows'] as $value) {
                if ((!isset($value['merge']) || $value['merge'] == true) && isset($varToMerge[$value['alias']])) {
                    $this->$value['alias'] = $varToMerge[$value['alias']];
                }
            }
            return true;
        } else if (is_null($varToMerge)) {
            return $this->merge(\Factory::Context()->post);
        } else if ($varToMerge instanceof \Abstract_DataObjects) {
            return $this->merge($varToMerge->getData());
        } else if ($varToMerge instanceof \Abstract_Form) {
            return $this->merge($varToMerge->getData());
        }
        
        return false;

    }

    /**
     * Slouzi pro ziskani dat pro metodu Collection::fromORM()
     * Nepouzivat primo
     */
    public function _fetchAll($order = null, $direction = null, $limit = null, $offset = null, $forceReload = false)
    {
        $additionalParams = array(
            'order' => $order,
            'smer' => $direction,
            'limit' => $limit,
            'offset' => $offset
        );

        return $this->get(true, false, $forceReload, $additionalParams);
    }

    /**
     * Vypise vsechny aktivni promenne, ktere trida momentalne obsahuje
     * @return string $return
     */
    public function dump_class_vars()
    {
        $return = '<pre>';
        foreach ($this->data as $key => $value) {
            $return .= $key . ' - ' . (is_object($value) ? get_class($value) : (is_array($value) ? (is_object(current($value)) ? 'Collection of ' .get_class(current($value)) : 'Array') : $value )). '<br/>';
        }
        $return .= '</pre>';
        return $return;
    }

    /**
     * Vypise vsechny aktivni promenne, ktere trida momentalne obsahuje
     * @return string $return
     */
    public function dump_class_config()
    {
        $return = '<pre>';
        $return .= print_r($this->config);
        $return .= '</pre>';

        return $return;
    }

    /**
     * Vrati tabulku kterou objekt vyuziva
     * @return string
     */
    public function getTable()
    {
        return $this->getConfigDbTable();
    }

    /**
     * Vrati server, ktery trida vyuziva pro svoji funkcnost
     * @return string
     */
    public function getServer()
    {
        return $this->getConfigDbServer();
    }

    /**
     * Vraci state tridy a promennou isLoaded
     * @return boolean
     */
    public function isLoaded()
    {
        return $this->isLoaded;
    }

    public function loadByPrimaryKey()
    {
        // TODO: Implement loadByPrimaryKey() method.
    }
}