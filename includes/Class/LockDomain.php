<?php

/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 1/23/2017
 * Time: 12:54 AM
 */
class LockDomain implements  DomainObject
{
    private $_lid;
    private $_roomID;
    private $_studentID;
    private $_Locktime;

    public function __construct()
    {

    }
    /**
     * @return mixed
     */
    public function getLid()
    {
        return $this->_lid;
    }

    /**
     * @param mixed $lid
     */
    public function setLid($lid)
    {
        $this->_lid = $lid;
    }

    /**
     * @return mixed
     */
    public function getRoomID()
    {
        return $this->_roomID;
    }

    /**
     * @param mixed $roomID
     */
    public function setRoomID($roomID)
    {
        $this->_roomID = $roomID;
    }

    /**
     * @return mixed
     */
    public function getStudentID()
    {
        return $this->_studentID;
    }

    /**
     * @param mixed $studentID
     */
    public function setStudentID($studentID)
    {
        $this->_studentID = $studentID;
    }

    /**
     * @return mixed
     */
    public function getLocktime()
    {
        return $this->_Locktime;
    }

    /**
     * @param mixed $Locktime
     */
    public function setLocktime($Locktime)
    {
        $this->_Locktime = $Locktime;
    }




}