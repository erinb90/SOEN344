<?php

/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2017-01-20
 * Time: 6:02 PM
 */
abstract class AbstractMapper
{


    /**
     * @param $data
     * @return stdClass
     */
    public abstract function getModel(stdClass $data);

    // TDG Communication methods
    /**
     * This method inserts row into database via tdg
     * @param \stdClass $object
     *
     */
    public abstract function insert(stdClass &$object);

    /**
     * This method deletes row into database via tdg
     * @param \stdClass $object
     *
     */
    public abstract function delete(stdClass &$object);

    /**
     *
     * This method updates row into database via tdg
     * @param \stdClass $object
     *
     */
    public abstract function update(stdClass &$object);

    // UOW methods
    /**
     * This method registers new object in unit of work
     * @param \stdClass $object
     *
     * @return AbstractMapper
     */
    public final function uowInsert(&$object)
    {
        UnitOfWork::registerNew($object, $this);
        return $this;
    }

    /**
     * This method registers deletion object in unit of work
     * @param \stdClass $object
     *
     * @return AbstractMapper
     */
    public final function uowDelete(&$object)
    {
        UnitOfWork::registerDeleted($object, $this);
        return $this;
    }

    /**
     * This method registers updated object in unit of work
     * @param \stdClass $object
     *
     * @return AbstractMapper
     */
    public final function uowUpdate(&$object)
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