<?php
namespace Stark\Interfaces;

/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-01-20
 * Time: 9:53 PM
 *
 * Interface containing method signatures for DB interaction methods
 */
interface Gateway
{

    /**
     * Insert new object into DB
     *
     * @param  DomainObject $object
     *
     * @return mixed
     */
    public function insert(DomainObject &$object);

    /**
     * Delete object from DB
     *
     * @param  DomainObject $object
     *
     * @return mixed
     */
    public function delete(DomainObject &$object);

    /**
     * Modify object in DB
     *
     * @param  DomainObject $object
     *
     * @return mixed
     */
    public function update(DomainObject &$object);

    /**
     * Find an entry in DB by its primary key
     *
     * @param int $id primary key
     *
     * @return mixed
     */
    public function findByPk($id);
}