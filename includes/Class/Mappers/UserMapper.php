<?php

namespace Stark\Mappers
{


    use Stark\Interfaces\AbstractMapper;
    use Stark\Interfaces\DomainObject;
    use Stark\Models\User;
    use Stark\TDG\UserTDG;

    /**
     * Mapper for User objects
     * Interacts with the UserTDG to retrieve and manipulate User objects from DB
     *
     * @package Stark\Mappers
     */
    class UserMapper extends AbstractMapper
    {

        /**
         * @var \Stark\TDG\UserTDG
         */
        private $_tdg;

        /**
         * UserMapper constructor.
         */
        public function __construct()
        {
            $this->_tdg = new UserTDG("users", "UserId");
        }

        /**
         * @return \Stark\TDG\UserTDG
         */
        public function getTdg()
        {
            return $this->_tdg;
        }

        /**
         * Retrieve all User objects from DB
         *
         * @return array
         */
        public function findAll()
        {
            $dbEntries = $this->getTdg()->findAll();

            $users = [];
            foreach ($dbEntries as $row)
            {
                $users[] = $this->getModel($row);
            }

            return $users;
        }

        /**
         * Find a user given its email
         *
         * @param $email
         *
         * @return \Stark\Models\User
         */
        public function findByEmail($email)
        {
            return $this->getModel($this->getTdg()->findByEmail($email)[0]);
        }

        /**
         * Creates a User object from a DB entry
         *
         * @param $data array data retrieve from the tdg
         *
         * @return User returns a fully-dressed object
         */
        public function getModel( array  $data = null)
        {

            if (!$data)
            {
                return NULL;
            }

            $User = new User();
            $User->setCapstoneStudent( $data['CapstoneStudent']);
            $User->setFirstName($data['FirstName']);
            $User->setLastName($data['LastName']);
            $User->setPassword($data['Password']);
            $User->setUserName($data['UserName']);
            $User->setUserId($data['UserId']);
            $User->setStudentId($data['StudentId']);

            return $User;

        }
    }
}