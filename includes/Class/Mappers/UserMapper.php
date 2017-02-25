<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 1:28 AM
 */

namespace Stark\Mappers
{


    use Stark\Interfaces\AbstractMapper;
    use Stark\Interfaces\DomainObject;
    use Stark\Models\User;
    use Stark\TDG\UserTDG;

    /**
     * Class UserMapper
     * @package Stark\Mappers
     */
    class UserMapper extends AbstractMapper
    {

        /**+
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
         * @return \Stark\Interfaces\TDG
         */
        public function getTdg()
        {
            return $this->_tdg;
        }

        /**
         * @return array
         */
        public function findAll()
        {
            $m = $this->getTdg()->findAll();

            $users = [];
            foreach ($m as $row)
            {
                $users[] = $this->getModel($row);
            }


            return $users;
        }

        /**
         * @param $email
         *
         * @return \Stark\Interfaces\DomainObject
         */
        public function findByEmail($email)
        {
            return $this->getModel($this->getTdg()->findByEmail($email)[0]);
        }

        /**
         * @param $data array data retrieve from the tdg
         *
         * @return DomainObject returns a fully-dressed object
         */
        public function getModel(array $data)
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