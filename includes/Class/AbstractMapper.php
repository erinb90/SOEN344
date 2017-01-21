<?php

/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2017-01-20
 * Time: 6:02 PM
 */
abstract class AbstractMapper implements Gateway
{


    public abstract function findByPk($id);
    /**
     * @param $data
     * @return stdClass
     */
    public abstract function getModel($data);

    // TDG Communication methods
    /**
     * This method inserts row into database via tdg
     * @param \DomainObject $object
     *
     */
    public abstract function insert(DomainObject &$object);

    /**
     * This method deletes row into database via tdg
     * @param \DomainObject $object
     *
     */
    public abstract function delete(DomainObject &$object);

    /**
     *
     * This method updates row into database via tdg
     * @param \DomainObject $object
     *
     */
    public abstract function update(DomainObject &$object);

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