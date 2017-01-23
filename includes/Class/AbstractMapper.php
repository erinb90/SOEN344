<?php

/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2017-01-20
 * Time: 6:02 PM
 */
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
     * @return \TDG
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
     * @param $data
     * @return DomainObject
     */
    public abstract function getModel($data);

    // TDG Communication methods
    /**
     * This method inserts row into database via tdg
     * @param \DomainObject $object
     * @return int
     */
    public function insert(DomainObject &$object)
    {
        return $this->getTdg()->insert($object);
    }

    /**
     * This method deletes row into database via tdg
     * @param \DomainObject $object
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
     * @param \DomainObject $object
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
     * @param \DomainObject $object
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
     * @param \DomainObject $object
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
     * @param \DomainObject $object
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