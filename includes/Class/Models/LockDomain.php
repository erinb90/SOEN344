<?php


namespace Stark\Models
{

    use Stark\Interfaces\DomainObject;

    /**
     * Class LockDomain
     * @package Stark\Models
     */
    class LockDomain implements DomainObject
    {

        private $_lockId;

        private $_RoomId;

        private $_UserId;

        private $_LockStartTime;

        private $_LockEndTime;

        /**
         * LockDomain constructor.
         */
        public function __construct()
        {

        }

        /**
         * @return mixed
         */
        public function getLockId()
        {
            return $this->_lockId;
        }

        /**
         * @param mixed $lockId
         */
        public function setLockId($lockId)
        {
            $this->_lockId = $lockId;
        }

        /**
         * @return mixed
         */
        public function getRoomId()
        {
            return $this->_RoomId;
        }

        /**
         * @param mixed $RoomId
         */
        public function setRoomId($RoomId)
        {
            $this->_RoomId = $RoomId;
        }

        /**
         * @return mixed
         */
        public function getUserId()
        {
            return $this->_UserId;
        }

        /**
         * @param mixed $UserId
         */
        public function setUserId($UserId)
        {
            $this->_UserId = $UserId;
        }

        /**
         * @return mixed
         */
        public function getLockStartTime()
        {
            return $this->_LockStartTime;
        }

        /**
         * @param mixed $LockStartTime
         */
        public function setLockStartTime($LockStartTime)
        {
            $this->_LockStartTime = $LockStartTime;
        }

        /**
         * @return mixed
         */
        public function getLockEndTime()
        {
            return $this->_LockEndTime;
        }

        /**
         * @param mixed $LockEndTime
         */
        public function setLockEndTime($LockEndTime)
        {
            $this->_LockEndTime = $LockEndTime;
        }


        /**
         * @return bool
         */
        public function isExpired()
        {
            return strtotime(date('Y-m-d H:i:s')) > strtotime($this->getLockEndTime());
        }

        /**
         * @return int
         */
        public function getRemainingSeconds()
        {
            return strtotime($this->getLockEndTime()) - strtotime(date('Y-m-d H:i:s'));
        }

    }
}