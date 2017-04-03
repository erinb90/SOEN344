<?php

namespace Stark
{


    use Stark\Mappers\ReservationMapper;

    class ReservationRegistry
    {
        /**
         * @var ReservationMapper
         */
        private $_reservationMapper;

        /**
         * ReservationRegistry constructor.
         */
        public function __construct()
        {
            $this->_reservationMapper = new ReservationMapper();
        }
        
        /**
         * @return array of Reservation objects
         */
        public function getReservations()
        {
            return $this->_reservationMapper->getReservations();
        }
    }
}