<?php

namespace Stark\TDG
{


    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Registry;

    /**
     * Class ReservationTDG
     * @package Stark\TDG
     */
    class ReservationTDG extends TDG
    {

        /**
         * @param $studentid
         *
         * @return array
         */
        public function findAllStudentReservations($studentid)
        {
            $query = Registry::getConnection()->createQueryBuilder();
            $query->select("*");
            $query->from($this->getTable());
            $query->where("UserId" . "=" . $studentid);
            $sth = $query->execute();
            $m = $sth->fetchAll();
            return $m;
        }

        /**
         * @return array
         */
        public function findAllWaitlisted()
        {
            $query = Registry::getConnection()->createQueryBuilder();
            $query->select("*");
            $query->from($this->getTable());
            $query->where("Waiting" . "=" . 1);
            $sth = $query->execute();
            $m = $sth->fetchAll();
            return $m;
        }

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
                Registry::getConnection()->insert($this->getTable(),
                    [
                        "UserId"     => $object->getUserId(),
                        "RoomId" => $object->getRoomId(),
                        "Starttime" => $object->getStartTimeDate(),
                        "Endtime" => $object->getEndTimeDate(),
                        "CreatedOn" => date('Y-m-d H:i:s'),
                        "Title" => $object->getTitle(),
                        "Waiting" => $object->isIsWaited()
                    ]
                );

                $lastId = Registry::getConnection()->lastInsertId();
                $object->setReservationId($lastId);
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
                        "RoomId" => $object->getRoomId(),
                        "Starttime" =>$object->getStartTimeDate(),
                        "Endtime" => $object->getEndTimeDate(),
                        "Title" => $object->getTitle(),
                        "Waiting" => $object->isIsWaited()
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