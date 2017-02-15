<?php

/**
 * Created by PhpStorm.
 * User: Server
 * Date: 1/21/2017
 * Time: 2:54 PM
 */
class WaitlistDomain implements DomainObject
{


    private $_wid;

    private $_studentID;

    private $_reservationID;

    /**
     * @return mixed
     */
    public function getWid()
    {
        return $this->_wid;
    }

    /**
     * @param mixed $wid
     */
    public function setWid($wid)
    {
        $this->_wid = $wid;
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
    public function getReservationID()
    {
        return $this->_reservationID;
    }

    /**
     * @param mixed $reservationID
     */
    public function setReservationID($reservationID)
    {
        $this->_reservationID = $reservationID;
    }


}