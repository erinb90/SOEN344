<?php
namespace Stark\TDG;
use Stark\Interfaces\DomainObject;
use Stark\Interfaces\TDG;

/**
 * Class RoomTDG
 */
class RoomTDG extends TDG
{

    /**
     * RoomTDG constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getPk()
    {
        return "roomID";
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return "room";
    }

    public function insert(DomainObject &$object)
    {
        // TODO: Implement insert() method.
    }

    public function delete(DomainObject &$object)
    {
        // TODO: Implement delete() method.
    }

    public function update(DomainObject &$object)
    {
        // TODO: Implement update() method.
    }

}
