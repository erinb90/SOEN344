<?php

/**
 * Created by PhpStorm.
 * User: Server
 * Date: 1/21/2017
 * Time: 1:02 PM
 */
class RoomDirectory
{

    private $_RoomMapper;

    public function __construct()
    {
        $this->_RoomMapper = new RoomMapper();
    }

    public function getRooms()
    {
        return $this->_RoomMapper->getAllRooms();

    }
}