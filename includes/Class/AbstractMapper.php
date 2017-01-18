<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016-11-20
 * Time: 6:02 PM
 */
abstract class AbstractMapper
{


    /**
     * @param $data
     * @return IDomainObject
     */
    public abstract function getModel($data);

    // TDG Communication methods
    /**
     * This method inserts row into database via tdg
     * @param \IDomainObject $object
     *
     */
    public abstract function insert(IDomainObject & $object);

    /**
     * This method deletes row into database via tdg
     * @param \IDomainObject $object
     *
     */
    public abstract function delete(IDomainObject & $object);

    /**
     *
     * This method updates row into database via tdg
     * @param \IDomainObject $object
     *
     */
    public abstract function update(IDomainObject & $object);

    // UOW methods
    /**
     * This method registers new object in unit of work
     * @param \IDomainObject $object
     *
     * @return AbstractMapper
     */
    public final function uowInsert(IDomainObject & $object)
    {
        UnitOfWork::registerNew($object, $this);
        return $this;
    }

    /**
     * This method registers deletion object in unit of work
     * @param \IDomainObject $object
     *
     * @return AbstractMapper
     */
    public final function uowDelete(IDomainObject & $object)
    {
        UnitOfWork::registerDeleted($object, $this);
        return $this;
    }

    /**
     * This method registers updated object in unit of work
     * @param \IDomainObject $object
     *
     * @return AbstractMapper
     */
    public final function uowUpdate(IDomainObject & $object)
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