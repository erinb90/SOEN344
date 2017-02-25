<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 12:37 AM
 */

namespace Stark\Mappers
{


    use Stark\Interfaces\AbstractMapper;
    use Stark\Interfaces\DomainObject;
    use Stark\Models\ConfirmedReservation;
    use Stark\TDG\ConfirmedReservationTDG;

    /**
     * Class ReservationMapper
     * @package Stark\Mappers
     */
    class ConfirmedReservationMapper extends AbstractMapper
    {

        private $_tdg;

        public function __construct()
        {
            $this->_tdg = new ConfirmedReservationTDG("confirmed_reservations", "ReservationId");
            $this->_tdg->setParentTable("reservations", "ReservationId");
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
         * @return DomainObject returns a fully-dressed object
         */
        public function getModel(array $data)
        {
            if (!$data)
            {
                return NULL;
            }


            $Reservation = new ConfirmedReservation();
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