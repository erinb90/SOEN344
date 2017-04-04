<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 1:19 AM
 */

namespace Stark\TDG
{


    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Registry;

    /**
     * Class RoomTDG
     * Performs DB calls for Rooms table
     * @package Stark\TDG
     */
    class RoomTDG extends TDG
    {

        /**
         * Insert a Room into DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Room $object
         *
         * @return int returns the last inserted id
         */
        public function insert(DomainObject &$object)
        {
            Registry::getConnection()->insert($this->getParentTable(),
                [
                    "Name"     => $object->getName(),
                    "Location" => $object->getLocation()
                ]
            );
        }

        /**
         * Delete a Room from DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Room $object
         *
         * @return bool
         */
        public function delete(DomainObject &$object)
        {
            Registry::getConnection()->delete($this->getTable(),
                [
                    $this->getPk() => $object->getRoomId()
                ]
            );
        }

        /**
         * Update a Room in DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Room $object
         *
         * @return bool
         */
        public function update(DomainObject &$object)
        {
            Registry::getConnection()->update(
                $this->getTable(),
                [
                    "Name"     => $object->getName(),
                    "Location" => $object->getLocation()
                ],
                [$this->getPk() => $object->getRoomId()]
            );
        }
    }
}