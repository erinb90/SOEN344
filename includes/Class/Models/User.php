<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 1:27 AM
 */

namespace Stark\Models
{


    use Stark\Interfaces\DomainObject;

    /**
     * Class User
     * @package Stark\Models
     */
    class User implements DomainObject
    {

        /**
         * @var
         */
        private $_UserId;

        /**
         * @var
         */
        private $_FirstName;

        /**
         * @var
         */
        private $_LastName;

        /**
         * @var
         */
        private $_UserName;

        /**
         * @var
         */
        private $_Password;

        /**
         * @var
         */
        private $_StudentId;

        /**
         * @var bool
         */
        private $_CapstoneStudent = FALSE;

        /**
         * @return mixed
         */
        public function getUserId()
        {
            return $this->_UserId;
        }


        /**
         * @return mixed
         */
        public function getFirstName()
        {
            return $this->_FirstName;
        }

        /**
         * @param mixed $FirstName
         */
        public function setFirstName($FirstName)
        {
            $this->_FirstName = $FirstName;
        }

        /**
         * @return mixed
         */
        public function getLastName()
        {
            return $this->_LastName;
        }

        /**
         * @param mixed $UserId
         */
        public function setUserId($UserId)
        {
            $this->_UserId = $UserId;
        }

        /**
         * @param mixed $LastName
         */
        public function setLastName($LastName)
        {
            $this->_LastName = $LastName;
        }

        /**
         * @return mixed
         */
        public function getStudentId()
        {
            return $this->_StudentId;
        }

        /**
         * @param mixed $StudentId
         */
        public function setStudentId($StudentId)
        {
            $this->_StudentId = $StudentId;
        }



        /**
         * @return mixed
         */
        public function getUserName()
        {
            return $this->_UserName;
        }

        /**
         * @param mixed $UserName
         */
        public function setUserName($UserName)
        {
            $this->_UserName = $UserName;
        }

        /**
         * @return mixed
         */
        public function getPassword()
        {
            return $this->_Password;
        }

        /**
         * @param mixed $Password
         */
        public function setPassword($Password)
        {
            $this->_Password = $Password;
        }

        /**
         * @return bool
         */
        public function isCapstoneStudent()
        {
            return $this->_CapstoneStudent;
        }

        /**
         * @param bool $CapstoneStudent
         */
        public function setCapstoneStudent($CapstoneStudent)
        {
            $this->_CapstoneStudent = $CapstoneStudent;
        }


    }
}