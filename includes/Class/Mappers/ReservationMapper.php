<?php

namespace Stark\Mappers
{

    use Stark\Interfaces\AbstractMapper;
    use Stark\Models\Reservation;
    use Stark\TDG\ReservationTDG;

    class ReservationMapper extends AbstractMapper
    {


        private $_tdg;

        public function __construct()
        {

        }

        /**
         * @return \Stark\Interfaces\TDG|\Stark\TDG\ConfirmedReservationTDG
         */
        public function getTdg()
        {
            return $this->_tdg;
        }

        public function findAllStudentReservations($userid)
        {
            $m = $this->getTdg()->findAllStudentReservations($userid);
            $objects = [];
            foreach ($m as $row)
            {
                $objects[] = $this->getModel($row);
            }

            return $objects;
        }

        /**
         * @param $data array data retrieve from the tdg
         *
         * @return Reservation returns a fully-dressed object
         */
        public function getModel(array $data)
        {
            if (!$data)
            {
                return NULL;
            }
            $Reservation = new Reservation();
            $Reservation->setRID($data['RoomId']);
            $Reservation->setStartTimeDate($data["Starttime"]);
            $Reservation->setEndTimeDate($data['Endtime']);
            $Reservation->setReservationID($data['ReservationId']);
            $Reservation->setCreatedOn($data['CreatedOn']);
            $Reservation->setTitle($data['Title']);
            $Reservation->setUserId($data['UserId']);

            return $Reservation;
        }
    }

}