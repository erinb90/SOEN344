<?php

namespace Stark\Models
{

    use Stark\Interfaces\DomainObject;


    /**
     * Class Reservation
     * @package Stark\Interfaces
     */
    class Reservation implements DomainObject
    {

        private $_reservationID;

        private $_roomId;

        private $_userId;

        private $_startTimeDate;

        private $_endTimeDate;

        private $_title;

        private $_createdOn;

        private $_isWaited = false;

        /**
         * Reservation constructor.
         */
        public function __construct()
        {
        }



        /**
         * @param mixed $reservationID
         */
        public function setReservationID($reservationID)
        {
            $this->_reservationID = $reservationID;
        }

        /**
         * @return mixed
         */
        public function getReservationID()
        {
            return $this->_reservationID;
        }

        /**
         * @return mixed
         */
        public function getUserId()
        {
            return $this->_userId;
        }

        /**
         * @param mixed $userId
         */
        public function setUserId($userId)
        {
            $this->_userId = $userId;
        }

        /**
         * @return mixed
         */
        public function getRoomId()
        {
            return $this->_roomId;
        }

        /**
         * @param mixed $rID
         */
        public function setRoomId($rID)
        {
            $this->_roomId = $rID;
        }

        /**
         * @return mixed
         */
        public function getStartTimeDate()
        {
            return $this->_startTimeDate;
        }

        /**
         * @param mixed $startTimeDate
         */
        public function setStartTimeDate($startTimeDate)
        {
            $this->_startTimeDate = $startTimeDate;
        }

        /**
         * @return mixed
         */
        public function getEndTimeDate()
        {
            return $this->_endTimeDate;
        }

        /**
         * @param mixed $endTimeDate
         */
        public function setEndTimeDate($endTimeDate)
        {
            $this->_endTimeDate = $endTimeDate;
        }

        /**
         * @return mixed
         */
        public function getTitle()
        {
            return $this->_title;
        }

        /**
         * @param mixed $title
         */
        public function setTitle($title)
        {
            $this->_title = $title;
        }

        /**
         * @return mixed
         */
        public function getCreatedOn()
        {
            return $this->_createdOn;
        }

        /**
         * @param mixed $createdOn
         */
        public function setCreatedOn($createdOn)
        {
            $this->_createdOn = $createdOn;
        }

        /**
         * @return bool
         */
        public function isIsWaited()
        {
            return $this->_isWaited == 1;
        }

        /**
         * @param bool $isWaited
         */
        public function setIsWaited($isWaited)
        {
            $this->_isWaited = $isWaited;
        }

    }
}