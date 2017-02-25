<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 1:04 AM
 */

namespace Stark\Mappers
{


    use Stark\Interfaces\AbstractMapper;
    use Stark\Interfaces\DomainObject;
    use Stark\Models\WaitlistedRservation;
    use Stark\TDG\WaitlistedReservationTDG;


    /**
     * Class WaitlistedReservationMapper
     * @package Stark\Mappers
     */
    class WaitlistedReservationMapper extends AbstractMapper
    {

        private $_tdg;

        /**
         * WaitlistedReservationMapper constructor.
         */
        public function __construct()
        {
            $this->_tdg = new WaitlistedReservationTDG("confirmed_reservations", "ReservationId");
            $this->_tdg->setParentTable("reservations", "ReservationId");
        }

        /**
         * @return \Stark\Interfaces\TDG|\Stark\TDG\ConfirmedReservationTDG
         */
        public function getTdg()
        {
            return $this->_tdg;
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


            $Reservation = new WaitlistedRservation();
            $Reservation->setRID($data['RoomId']);
            $Reservation->setStartTimeDate($data["Starttime"]);
            $Reservation->setEndTimeDate($data['Endtime']);
            $Reservation->setCreatedOn($data['CreatedOn']);
            $Reservation->setTitle($data['Title']);
            $Reservation->setReservationID($data['ReservationId']);
            $Reservation->setUserId($data['UserId']);

            return $Reservation;
        }
    }
}