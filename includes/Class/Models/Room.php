<?php
namespace Stark\Models;

use Stark\Interfaces\DomainObject;

/**
 * Class RoomDomain
 */
class Room implements DomainObject
{

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_location;

    /**
     * @var int
     */
    private $_roomID;

    private $_Color;


    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->_location;
    }


    /**
     * @param mixed $Color
     */
    public function setColor($Color)
    {
        $this->_Color = $Color;
    }

    /**
     * @return int
     */
    public function getRoomId()
    {
        return $this->_roomID;
    }

    /**
     * @param $name string
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @param $location string
     */
    public function setLocation($location)
    {
        $this->_location = $location;
    }


    /**
     * @param int $roomID
     */
    public function setRoomID($roomID)
    {
        $this->_roomID = $roomID;
    }


    public function getColor()
    {
        return $this->_Color;
    }
}