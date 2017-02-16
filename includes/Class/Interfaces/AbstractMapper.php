<?php
namespace Stark\Interfaces;
use Stark\UnitOfWork;
use Stark\Interfaces\TDG;

abstract class AbstractMapper implements Gateway
{
    /**
     * AbstractMapper constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * @return \Stark\Interfaces\TDG
     */
    public abstract function getTdg();

    /**.
     * @param $id
     *
     * @return mixed
     */
    public function findByPk($id)
    {
        return $this->getModel($this->getTdg()->findByPk($id));
    }

    /**
     * @param $data array data retrieve from the tdg
     * @return DomainObject returns a fully-dressed object
     */
    public abstract function getModel($data);
    /**
     * This method inserts row into database via tdg
     * @param DomainObject $object
     * @return int
     */
    public function insert(DomainObject &$object)
    {
        return $this->getTdg()->insert($object);
    }

    /**
     * This method deletes row into database via tdg
     * @param  DomainObject $object
     * @return bool
     *
     */
    public function delete(DomainObject &$object)
    {
        return $this->getTdg()->delete($object);
    }

    /**
     *
     * This method updates row into database via tdg
     * @param DomainObject $object
     * @return bool
     *
     */
    public function update(DomainObject &$object)
    {
        return $this->getTdg()->update($object);
    }

    // UOW methods
    /**
     * This method registers new object in unit of work
     * @param  DomainObject $object
     *
     * @return AbstractMapper
     */
    public final function uowInsert(DomainObject &$object)
    {
        UnitOfWork::registerNew($object, $this);
        return $this;
    }

    /**
     * This method registers deletion object in unit of work
     * @param  DomainObject $object
     *
     * @return AbstractMapper
     */
    public final function uowDelete(DomainObject &$object)
    {
        UnitOfWork::registerDeleted($object, $this);
        return $this;
    }

    /**
     * This method registers updated object in unit of work
     * @param  DomainObject $object
     *
     * @return AbstractMapper
     */
    public final function uowUpdate(DomainObject &$object)
    {
        UnitOfWork::registerDirty($object, $this);
        return $this;
    }

    /**
     * Calls commit on unit of work
     * @return bool
     */
    public final function commit()
    {
        return UnitOfWork::commit();
    }



}