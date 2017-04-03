<?php

namespace Stark\TDG
{


    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Registry;

    /**
     * Performs DB calls for Reservation table
     * Class ReservationTDG
     * @package Stark\TDG
     */
    class ReservationTDG extends TDG
    {

        /**
         * Find a Reservation given a UserId
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
         * Returns all waitlisted reservations
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
         * Returns all active reservations
         * @return array
         */
        public function findAllActive()
        {
            $query = Registry::getConnection()->createQueryBuilder();
            $query->select("*");
            $query->from($this->getTable());
            $query->where("Waiting" . "=" . 0);
            $sth = $query->execute();
            $m = $sth->fetchAll();
            return $m;
        }

        /**
         * Inserts a Reservation object into DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Reservation $object
         *
         * @return int returns the last inserted id
         */
        public function insert(DomainObject &$object)
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
        }

        /**
         * Delete a Reservation from DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Reservation $object
         *
         * @return bool
         */
        public function delete(DomainObject &$object)
        {
            Registry::getConnection()->delete($this->getTable(),
                [
                    $this->getPk() => $object->getReservationID()
                ]
            );
        }


        /**
         * Update a Reservation in DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Reservation $object
         *
         * @return bool
         */
        public function update(DomainObject &$object)
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
        }
    }
}