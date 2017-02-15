<?php

/**
 * Class RoomDomain
 */
class RoomDomain implements DomainObject
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
     * @var string
     */
    private $_description;

    /**
     * @var int
     */
    private $_roomID;


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
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }


    /**
     * @return int
     */
    public function getRID()
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
     * @param $desc
     */
    public function setDescription($desc)
    {
        $this->_description = $desc;
    }

    /**
     * @param $roomid int room id
     */
    public function setRID($roomid)
    {
        $this->_roomID = $roomid;
    }


}