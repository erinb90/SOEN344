<?php

/**
 * Created by PhpStorm.
 * User: Server
 * Date: 1/21/2017
 * Time: 1:02 PM
 */
class RoomDirectory
{

    /**
     * @var \RoomMapper
     */
    private $_RoomMapper;

    /**
     * RoomDirectory constructor.
     */
    public function __construct()
    {
        $this->_RoomMapper = new RoomMapper();
    }

    /**
     * @return array returns a list of RoomDomain objects
     */
    public function getRooms()
    {
        return $this->_RoomMapper->getAllRooms();

    }
}