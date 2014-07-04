<?php

namespace ObjectRelationMapper;

abstract class Common extends Base\AORM
{
    /**
     * Zvaliduje vsechny hodnoty | jednu hodnotu ORMka oproti definici jeho sloupce
     * @param null $property
     * @return bool
     */
    public function validate($property = NULL)
    {
        $return = true;

        if(!is_null($property)){
            $return = $this->aliases[$property]->validate($this->{$property});
        } else {
            foreach($this as $property => $value){
                if($this->aliases[$property]->validate($value) == false){
                    $return = false;
                    break;
                }
            }
        }

        return $return;
    }

    /**
     * Spocita, kolik zadanych radku odpovida nastavenym properties
     * @return int
     */
    public function count()
    {
        return $this->queryBuilder->count($this);
    }

    /**
     * Insert Dat
     * @return bool
     */
    protected function insert()
    {
        if(method_exists($this, 'beforeInsert') && $this->beforeInsert() === false){
            return false;
        }

        $this->queryBuilder->insert($this);

        $this->changedVariables = Array();

        if(method_exists($this, 'afterInsert') && $this->afterInsert() === false){
            return false;
        }
    }

    /**
     * Update dat dle PK
     * @return bool
     */
    protected function update()
    {
        if(method_exists($this, 'beforeUpdate') && $this->beforeUpdate() === false){
            return false;
        }

        $this->queryBuilder->update($this, $this->changedPrimaryKey);

        $this->changedVariables = Array();

        if(method_exists($this, 'afterUpdate') && $this->afterUpdate() === false){
            return false;
        }
    }

    /**
     * Vrati danou property prvniho childa ve formatu child.property[, ["$param1", "$paramx"]]
     * @example $orm->cProperty('user.name')
     * @example $orm->cProperty('user.getAllRights, [", ", "//", "adsfdsaf"]')
     * @param $string
     * @throws Exception\ORM
     * @return string
     */
    public function cProperty($string)
    {
        if(!preg_match('/^(.*)\.(.*?)(\,(.*))?$/', $string, $matches)){
            throw new Exception\ORM('Vyber child property musi byt ve formatu child.property[, ["$param1", "$paramx"]]');
        }

        if(!isset($this->childs[$matches[1]])){
            throw new Exception\ORM('Child '. $matches[1] . ' neni nadefinovan.');
        }

        if(!isset($this->childsData[$matches[1]])){
            $this->children($matches[1]);
        }

        if(isset($this->childsData[$matches[1]][0]->{$matches[2]})){
            return $this->childsData[$matches[1]][0]->{$matches[2]};
        } elseif(isset($this->childsData[$matches[1]][0]) && method_exists($this->childsData[$matches[1]][0], $matches[2])) {
            if(isset($matches[3]) && isset($matches[4])){
                preg_match_all('/([\'"])(.*?)([\'"])/i', $matches[4], $params);
                return call_user_func_array(Array($this->childsData[$matches[1]][0], $matches[2]), $params[2]);
            } else {
                return $this->childsData[$matches[1]][0]->{$matches[2]}();
            }

        } else {
            return NULL;
        }
    }

	/**
	 * Nahraje objekt z daneho storage
	 * @throws Exception\ORM
	 * @return boolean|mixed
	 */
	public function loadByPrimaryKey()
	{
		if(!isset($this->primaryKey) || empty($this->primaryKey)){
			throw new Exception\ORM('Nelze loadnout orm dle primarniho klice, protoze primarni klic neni nastaven.');
		}

		if(method_exists($this, 'beforeLoad') && $this->beforeLoad() === false){
			return false;
		}

		$this->loadClassFromArray($this->queryBuilder->loadByPrimaryKey($this));

		$this->changedVariables = Array();

		if(method_exists($this, 'afterLoad') && $this->afterLoad() === false){
			return false;
		}
	}

    /**
     * Vrati danou property vsech childu ve formatu child.property
     * @example $orm->cProperties('report.time')
     * @param $string
     * @param null $glue
     * @throws Exception\ORM
     * @return string
     */
    public function cProperties($string, $glue = NULL)
    {
        if(!preg_match('/^(.*)\.(.*)$/', $string, $matches)){
            throw new Exception\ORM('Vyber child property musi byt ve formatu child.property');
        }

        if(!isset($this->childs[$matches[1]])){
            throw new Exception\ORM('Child '. $matches[1] . ' neni nadefinovan.');
        }

        if(!isset($this->childsData[$matches[1]])){
            $this->children($matches[1]);
        }

        $return = Array();

        foreach($this->childsData[$matches[1]] as $key => $child){
            $return[$key] = $child->{$matches[2]};
        }

        if(!is_null($glue)){
            return implode($glue, $return);
        } else {
            return $return;
        }
    }
}