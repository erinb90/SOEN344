<?php

namespace Stark
{


    use Stark\Mappers\ReservationMapper;

    class ReservationRegistry
    {
        /**
         * @var array of Reservation objects
         */
        private $reservations;
        /**
         * ReservationRegistry constructor.
         */
        public function __construct()
        {

        }
        /**
         * @return array of Reservation objects
         */
        public function getReservations()
        {
            $ReservationMapper = new ReservationMapper();
            $_Reservations = $ReservationMapper->getReservations();
            return $_Reservations;
        }
    }
}