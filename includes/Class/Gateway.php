<?php

/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-01-20
 * Time: 9:53 PM
 */
interface Gateway
{
    public  function insert(DomainObject &$object);
    public  function delete(DomainObject &$object);
    public  function update(DomainObject &$object);
    public  function findByPk($id);
}