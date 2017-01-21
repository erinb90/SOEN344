<?php

/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-01-20
 * Time: 9:53 PM
 */
interface Gateway
{
    public  function insert(stdClass &$object);
    public  function delete(stdClass &$object);
    public  function update(stdClass &$object);
    public  function findByPk($id);
}