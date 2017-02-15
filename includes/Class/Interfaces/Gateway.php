<?php

/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-01-20
 * Time: 9:53 PM
 */
interface Gateway
{

    /**
     * @param \DomainObject $object
     *
     * @return mixed
     */
    public  function insert(DomainObject &$object);

    /**
     * @param \DomainObject $object
     *
     * @return mixed
     */
    public  function delete(DomainObject &$object);

    /**
     * @param \DomainObject $object
     *
     * @return mixed
     */
    public  function update(DomainObject &$object);

    /**
     * @param int $id primary key
     *
     * @return mixed
     */
    public  function findByPk($id);
}