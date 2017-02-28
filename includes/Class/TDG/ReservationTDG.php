<?php

namespace Stark\TDG
{


    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Registry;

    class ReservationTDG extends TDG
    {

        /**
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Reservation $object
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
                        "UserId"     => $object->getUserId(),
                        "RoomId" => $object->getRID(),
                        "Starttime" => $object->getStartTimeDate(),
                        "Endtime" => $object->getEndTimeDate(),
                        "CreatedOn" => date("Y-M-D H:i:s"),
                        "Title" => $object->getTitle()
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
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Reservation $object
         *
         * @return mixed
         */
        public function delete(DomainObject &$object)
        {
            return Registry::getConnection()->delete($this->getTable(),
                [
                    $this->getPk() => $object->getReservationID()
                ]
            );
        }

        /**
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Reservation $object
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
                        "UserId"     => $object->getUserId(),
                        "RoomId" => $object->getRID(),
                        "Starttime" =>$object->getStartTimeDate(),
                        "Endtime" => $object->getEndTimeDate(),
                        "Title" => $object->getTitle()
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