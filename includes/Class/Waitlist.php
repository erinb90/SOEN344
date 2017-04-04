<?php

namespace Stark
{


    use Stark\Mappers\ReservationMapper;
    use Stark\Mappers\UserMapper;
    use Stark\Models\Reservation;
    use Stark\Models\User;

    /**
     * Class Waitlist
     * @package Stark
     */
    class Waitlist
    {

        private $_RoomId;

        private $_startTime;

        private $_endTime;

        private $_ReservationMapper;

        /**
         * @var array
         */
        private $_reservations = [];

        /**
         * Waitlist constructor.
         *
         * @param $roomId
         * @param $startTime
         * @param $endTime
         */
        public function __construct($roomId, $startTime, $endTime)
        {
            $this->_RoomId = $roomId;

            $this->_startTime = $startTime;

            $this->_endTime = $endTime;

            $this->_ReservationMapper = new ReservationMapper();
        }

        /**
         *
         */
        //Todo: should refactor this into a separate class that finds reservations between timeslots. This is used a lot throughout application
        private function findSamereservationTimes()
        {
            $reservations = $this->_ReservationMapper->findAllWaitlisted();
            $start = strtotime($this->_startTime);
            $end = strtotime($this->_endTime);


            /**
             * @var $Reservation \Stark\Models\Reservation
             */
            foreach ($reservations as $Reservation)
            {

                $startTime = strtotime($Reservation->getStartTimeDate());
                $endTime = strtotime($Reservation->getEndTimeDate());
                // was the start time of current reservation between the conflicted start and end time period?
                if ($start >= $startTime && $start < $endTime)
                {
                    $this->_reservations[$Reservation->getReservationID()] = $Reservation;
                }
                // was the end time of current reservation between the conflicted start and end time period?
                if ($end > $startTime && $end <= $endTime)
                {
                    $this->_reservations[$Reservation->getReservationID()] = $Reservation;
                }
                // is the current reservation start time less than the one reserved and is the end time of the one reserved less than the current end time
                if ($startTime >= $start && $end >= $endTime)
                {
                    $this->_reservations[$Reservation->getReservationID()] = $Reservation;
                }
            }
        }

        /**
         * sort descending by primary key since this determines the order these reservations were created
         */
        private function sortReservations()
        {
            ksort($this->_reservations);
        }

        /**
         * Returns a list of reservations based on time slot
         * @return array
         */
        public function getWaitlistedReservations()
        {
            return $this->_reservations;
        }


        /**
         * Returns the next student in line for a reservation based on the time slot
         * @return Reservation[]|null
         */
        public function getNextReservationsWaiting()
        {
            // find all reservations bounded by this time slot
            $this->findSamereservationTimes();
            $this->sortReservations();

            if(empty($this->getWaitlistedReservations()))
                return null;

            $UserMapper = new UserMapper();


            // cache the students
            $capstoneStudentsReservation = [];
            $regularStudentsReservation = [];

            /**
             * @var $Reservation Reservation
             */
            foreach($this->getWaitlistedReservations() as $reservationId => $Reservation)
            {
                $userId = $Reservation->getUserId();
                /**
                 * @var $User User
                 */
                $User = $UserMapper->findByPk($userId);

                // sort these users
                if($User->isCapstoneStudent())
                {
                    $capstoneStudentsReservation[] = $Reservation;
                }
                else
                {
                    $regularStudentsReservation[] = $Reservation;
                }

            }

            // if capstone students were found, return first one in list
            if(!empty($capstoneStudentsReservation))
            {
                return array_merge($capstoneStudentsReservation, $regularStudentsReservation);
            }

            return $regularStudentsReservation;
        }
    }
}