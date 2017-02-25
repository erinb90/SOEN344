<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 12:56 AM
 */

namespace Stark\Interfaces
{


    /**
     * Class Reservation
     * @package Stark\Interfaces
     */
    abstract class Reservation implements DomainObject
    {

        private $_reservationID;

        private $_rID;

        private $_userId;

        private $_startTimeDate;

        private $_endTimeDate;

        private $_title;

        private $_createdOn;


        /**
         * Reservation constructor.
         */
        public function __construct()
        {
        }

        /**
         * @return mixed
         */
        public function getReID()
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
        public function getRID()
        {
            return $this->_rID;
        }

        /**
         * @param mixed $rID
         */
        public function setRID($rID)
        {
            $this->_rID = $rID;
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

    }
}