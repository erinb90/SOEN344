<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016-11-20
 * Time: 5:12 PM
 */
interface IUnitOfWork
{
    /**
     * @param IDomainObject $object
     * @param AbstractMapper $mapper
     * @return mixed
     */
    public static function registerNew($object, AbstractMapper &$mapper);

    /**
     * @param IDomainObject $object
     * @param AbstractMapper $mapper
     * @return mixed
     */
    public static function registerDirty($object, AbstractMapper &$mapper);

    /**
     * @param IDomainObject $object
     * @param AbstractMapper $mapper
     * @return mixed
     */
    public static function registerDeleted($object, AbstractMapper &$mapper);

    /**
     * @return mixed
     */
    public static function commit();
}

?>