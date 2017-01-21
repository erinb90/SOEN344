<?php

/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-01-18
 * Time: 7:55 PM
 */
abstract class TDG implements Gateway
{

    public abstract function getPk();
    public abstract function getTable();
    public abstract function insert(stdClass &$object);
    public abstract function delete(stdClass &$object);
    public abstract function update(stdClass &$object);
    public abstract function findByPk($id);

}