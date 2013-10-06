<?php

/**
 * Class ObjectRelationMapper
 *
 * Ocekavana funkcnost
 *
 * $o = new ORM_Zadatel();
 * $o->jmeno = 'Franta';
 * $o->save();
 *
 * $o->load();
 * $o->delete();
 */

abstract class ObjectRelationMapper implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * @var ObjectRelationMapper_QueryBuilder
     */
    protected $queryBuilder;

    public function __construct($id)
    {
        $this->setUp();
    }

    public function addQueryBuilder(ObjectRelationMapper_QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    abstract protected function setUp();
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     */
    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset
     * @return boolean true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * @param mixed $value <p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */
    public function count()
    {
        // TODO: Implement count() method.
    }
}