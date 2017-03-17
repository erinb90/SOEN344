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
            Registry::getConnection()->beginTransaction();
            $lastId = -1;
            try
            {
                Registry::getConnection()->insert($this->getParentTable(),
                    [
                        "Name"     => $object->getName(),
                        "Location" => $object->getLocation()
                    ]
                );

                $lastId = Registry::getConnection()->lastInsertId();
                Registry::getConnection()->commit();

            }
            catch (\Exception $e)
            {
                Registry::getConnection()->rollBack();
            }

            return $lastId;
        }

        /**
         * Delete a Room from DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Room $object
         *
         * @return bool
         */
        public function delete(DomainObject &$object)
        {
            try
            {
                Registry::getConnection()->delete($this->getTable(),
                    [
                        $this->getPk() => $object->getRoomId()
                    ]
                );
                return true;
            }
            catch (\Exception $e)
            {

            }
            return false;

        }

        /**
         * Update a Room in DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Room $object
         *
         * @return bool
         */
        public function update(DomainObject &$object)
        {

            try
            {
                Registry::getConnection()->update(
                    $this->getTable(),
                    [
                        "Name"     => $object->getName(),
                        "Location" => $object->getLocation()
                    ],
                    [$this->getPk() => $object->getRoomId()]
                );

                return TRUE;
            }
            catch (\Exception $e)
            {

            }

            return FALSE;

        }
    }
}