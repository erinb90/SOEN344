<?php

namespace Stark\Mappers
{

    use Stark\Interfaces\AbstractMapper;
    use Stark\Models\Reservation;
    use Stark\TDG\ReservationTDG;

    class ReservationMapper extends AbstractMapper
    {


        /**
         * @var \Stark\TDG\ReservationTDG
         */
        private $_tdg;

        public function __construct()
        {

            $this->_tdg = new ReservationTDG("reservations","ReservationId");
        }

        /**
         * @return \Stark\Interfaces\TDG|\Stark\TDG\ReservationTDG
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
        public function getModel(array $data = null)
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