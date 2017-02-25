<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 1:06 AM
 */

namespace Stark\TDG
{


    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Registry;

    /**
     * Class WaitlistedReservationTDG
     * @package Stark\TDG
     */
    class WaitlistedReservationTDG extends TDG
    {

        /**
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\WaitlistedRservation $object
         *
         * @return int|string
         * @throws \Exception
         */
        public function insert(DomainObject &$object)
        {
            Registry::getConnection()->beginTransaction();
            $lastId = -1;

            try
            {
                Registry::getConnection()->insert($this->getParentTable(),
                    [
                        "UserId"    => $object->getUserId(),
                        "RoomId"    => $object->getRID(),
                        "Starttime" => $object->getStartTimeDate(),
                        "Endtime"   => $object->getEndTimeDate(),
                        "CreatedOn" => $object->getCreatedOn()
                    ]
                );

                $lastId = Registry::getConnection()->lastInsertId();

                Registry::getConnection()->insert($this->getTable(),
                    [
                        "ReservationId" => $object->getReservationID()
                    ]
                );


                Registry::getConnection()->commit();

            }
            catch (\Exception $e)
            {
                Registry::getConnection()->rollBack();
                throw new \Exception("Cannot insert reservation into database: " . $e->getMessage());
            }

            return $lastId;
        }

        /**
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\WaitlistedRservation $object
         *
         * @return mixed
         */
        public function delete(DomainObject &$object)
        {
            // This works under the assumption that CASCADE DELETE is on
            return Registry::getConnection()->delete($this->getParentTable(),
                [
                    $this->getParentPk() => $object->getReservationID()
                ]
            );
        }

        /**
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\WaitlistedRservation $object
         *
         * @return bool
         * @throws \Exception
         */
        public function update(DomainObject &$object)
        {
            try
            {
                Registry::getConnection()->update(
                    $this->getTable(),
                    [
                        "UserId"    => $object->getUserId(),
                        "RoomId"    => $object->getRID(),
                        "Starttime" => $object->getStartTimeDate(),
                        "Endtime"   => $object->getEndTimeDate(),
                        "CreatedOn" => $object->getCreatedOn()
                    ],
                    [$this->getPk() => $object->getReservationID()]
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