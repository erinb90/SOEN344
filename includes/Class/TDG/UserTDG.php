<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 1:29 AM
 */

namespace Stark\TDG
{


    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Registry;

    /**
     * Class UserTDG
     * Performs DB calls for Users table
     * @package Stark\TDG
     */
    class UserTDG extends TDG
    {

        /**
         * Find a user given an email address
         * @param $email
         *
         * @return array
         */
        public function findByEmail($email)
        {

            return $this->query("*", ["UserName" => $email]);
        }

        /**
         * Insert a User into DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\User $object
         *
         * @return int returns the last inserted id
         */
        public function insert(DomainObject &$object)
        {
            Registry::getConnection()->insert($this->getParentTable(),
                [
                    "FirstName"       => $object->getFirstName(),
                    "LastName"        => $object->getLastName(),
                    "Password"        => $object->getPassword(),
                    "StudentId"       => $object->getStudentId(),
                    "CapstoneStudent" => $object->isCapstoneStudent()
                ]
            );
        }

        /**
         * Delete a User from DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\User $object
         *
         * @return bool
         */
        public function delete(DomainObject &$object)
        {
            Registry::getConnection()->delete($this->getTable(),
                [
                    $this->getPk() => $object->getUserId()
                ]
            );
        }

        /**
         * Update a User in DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\User $object
         *
         * @return bool
         */
        public function update(DomainObject &$object)
        {
            Registry::getConnection()->update(
                $this->getTable(),
                [
                    "FirstName"       => $object->getFirstName(),
                    "LastName"        => $object->getLastName(),
                    "Password"        => $object->getPassword(),
                    "StudentId"       => $object->getStudentId(),
                    "CapstoneStudent" => $object->isCapstoneStudent()
                ],
                [$this->getPk() => $object->getUserId()]
            );
        }
    }
}