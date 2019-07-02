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

		if ($property != NULL) {
			$return = $this->aliases[$property]->validate($this->{$property});
		} else {
			foreach ($this as $property => $value) {
				if ($this->aliases[$property]->validate($value) == false) {
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
     * Truncatne danou tabulku, ke ktere je ORM prirazeno
     * @return int
     */
    public function truncate()
    {
        return $this->queryBuilder->truncate($this);
    }

	/**
	 * Vrati danou property prvniho childa ve formatu child.property[, ["$param1", "$paramx"]]
	 * @example $orm->cProperty('user.name')
	 * @example $orm->cProperty('user.getAllRights, [", ", "//", "adsfdsaf"]')
	 * @param string $string
	 * @throws Exception\ORM
	 * @return string
	 */
	public function cProperty($string)
	{
		if (!preg_match('/^(.*)\.(.*?)(\,(.*))?$/', $string, $matches)) {
			throw new Exception\ORM('Vyber child property musi byt ve formatu child.property[, ["$param1", "$paramx"]]');
		}

		if (!isset($this->childs[$matches[1]])) {
			throw new Exception\ORM('Child ' . $matches[1] . ' neni nadefinovan.');
		}

		if (!isset($this->childsData[$matches[1]])) {
			$this->children($matches[1]);
		}

		if (!is_array($this->childsData[$matches[1]])){
            throw new Exception\ORM('For cProperty to work, childsData['.$matches[1].'] needs to be an array');
        }

		if (isset($this->childsData[$matches[1]][0]->{$matches[2]})) {
			return $this->childsData[$matches[1]][0]->{$matches[2]};
		} elseif (isset($this->childsData[$matches[1]][0]) && method_exists($this->childsData[$matches[1]][0], $matches[2])) {
			if (isset($matches[3]) && isset($matches[4])) {
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
	 * Vrati kolekci ze zadaneho dotazu
	 * @param array $loadData
	 * @return array
	 */
	public function loadMultiple($loadData = NULL)
	{
		if ($this->limitOverride === false) {
			$this->setOrderingLimit(9999999999);
		}

		if (is_null($loadData)) {
			$collection = $this->queryBuilder->loadMultiple($this);
		} else {
			$collection = & $loadData;
		}

		$return = Array();
		$object = $this->getConfigObject();

		foreach ($collection as $singleOrm) {
			$tempOrm = new $object();
			$tempOrm->load($singleOrm);
			$return[] = $tempOrm;
		}

		return $return;
	}

	/**
	 * Nahraje z ORMka pouze properties, ktere jsme nastavili v aliasu
	 * @param string $alias
	 */
	public function loadMFU($alias, $loadData = NULL)
	{
		$this->mfuActive = $alias;
		$return = $this->load($loadData);
		$this->mfuActive = false;
		return $return;
	}

	/**
	 * Nahraje z ORMka pouze properties, ktere jsme nastavili v aliasu
	 * @param string $alias
	 * @return array
	 */
	public function loadMultipleMFU($alias, $loadData = NULL)
	{
		$this->mfuActive = $alias;
		$return = $this->loadMultiple($loadData);
		$this->mfuActive = false;
		return $return;
	}


	/**
	 * Vrati danou property vsech childu ve formatu child.property
	 * @example $orm->cProperties('report.time')
	 * @param string $string
	 * @param null $glue
	 * @throws Exception\ORM
	 * @return string|array
	 */
	public function cProperties($string, $glue = NULL)
	{
		if (!preg_match('/^(.*)\.(.*)$/', $string, $matches)) {
			throw new Exception\ORM('Vyber child property musi byt ve formatu child.property');
		}

		if (!isset($this->childs[$matches[1]])) {
			throw new Exception\ORM('Child ' . $matches[1] . ' neni nadefinovan.');
		}

		if (!isset($this->childsData[$matches[1]])) {
			$this->children($matches[1]);
		}

		$return = Array();

		foreach ($this->childsData[$matches[1]] as $key => $child) {
			$return[$key] = $child->{$matches[2]};
		}

		if ($glue != NULL) {
			return implode($glue, $return);
		} else {
			return $return;
		}
	}

    /**
     * Vrati pole ve formatu ['report.time'] = 'report.value'
     * @example $orm->cProperties('report.time', 'report.value')
     * @param string $string
     * @param null $glue
     * @throws Exception\ORM
     * @return array
     */
    public function cPropertiesColumn($key, $value)
    {
        if (!preg_match('/^(.*)\.(.*)$/', $key, $keyM) || !preg_match('/^(.*)\.(.*)$/', $value, $valueM) ) {
            throw new Exception\ORM('Vyber child property musi byt ve formatu child.property');
        }

        if ($keyM[1] != $valueM[1]){
            throw new Exception\ORM('Klic a hodnota musi obe pochazet ze stejneho childa');
        }

        if (!isset($this->childs[$keyM[1]])) {
            throw new Exception\ORM('Child ' . $keyM[1] . ' neni nadefinovan.');
        }

        if (!isset($this->childsData[$keyM[1]])) {
            $this->children($keyM[1]);
        }

        $return = Array();

        foreach ($this->childsData[$keyM[1]] as $index => $child) {
            $return[$child->{$keyM[2]}] = $child->{$valueM[2]};
        }

        return $return;
    }

	/**
	 * Nahraje count pres danou query
	 * @param string $query
	 * @param array $params
	 * @return int
	 * @throws Exception\ORM
	 */
	public function countByQuery($query, $params)
	{
		if (empty($query)) {
			throw new Exception\ORM('Nemohu loadovat pres prazdnou query.');
		}

		return $this->queryBuilder->countByQuery($this, $query, $params);
	}


	/**
	 * Invokne dane metody
	 * @param array $arCallArray
	 * @return bool
	 */
	protected function internalCalls(Array $arCallArray)
	{
		foreach($arCallArray as $callableMethod){
			if(is_callable($callableMethod)){
				call_user_func($callableMethod);
			}
		}

		return true;
	}
}
