<?php
namespace Stark\Interfaces;
use Stark\Registry;


/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-01-18
 * Time: 7:55 PM
 */
abstract class TDG implements Gateway
{

    /**
     * @return mixed
     */
    public abstract function getPk();

    /**
     * @return string
     */
    public abstract function getTable();

    /**
     * @param \Stark\Interfaces\DomainObject $object
     *
     * @return int
     */
    public abstract function insert(DomainObject &$object);

    /**
     * @param \Stark\Interfaces\DomainObject $object
     *
     * @return mixed
     */
    public abstract function delete(DomainObject &$object);

    /**
     * @param \Stark\Interfaces\DomainObject $object
     *
     * @return mixed
     */
    public abstract function update(DomainObject &$object);
    /**
     * @param $id
     *
     * @return array
     */
    public function findByPk($id)
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable(), $this->getTable());
        $query->where($this->getTable() . '.' . $this->getPk() . "='" . $id . "'");
        $sth = $query->execute();
        $m = $sth->fetchAll();
        return $m[0];
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable());
        $sth = $query->execute();
        $m = $sth->fetchAll();
        return $m;
    }

}