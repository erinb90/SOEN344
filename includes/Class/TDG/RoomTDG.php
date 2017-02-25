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
     * @package Stark\TDG
     */
    class RoomTDG extends TDG
    {

        /**
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
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Room $object
         *
         * @return mixed
         */
        public function delete(DomainObject &$object)
        {

            return Registry::getConnection()->delete($this->getTable(),
                [
                    $this->getPk() => $object->getRoomId()
                ]
            );
        }

        /**
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