<?php

namespace Stark\Mappers {
    
    use Stark\Interfaces\AbstractMapper;
    use Stark\Models\Reservation;
    use Stark\TDG\ReservationTDG;
    use Stark\Utilities;


    /**
     * Mapper for Reservation objects
     * Interacts with the ReservationTDG to retrieve and manipulate Reservation objects from DB
     *
     * @package Stark\Mappers
     */
    class ReservationMapper extends AbstractMapper
    {


        /**
         * @var \Stark\TDG\ReservationTDG
         */
        private $_tdg;

        /**
         * ReservationMapper constructor.
         */
        public function __construct()
        {

            $this->_tdg = new ReservationTDG("reservations", "ReservationId");
        }

        /**
         * @return \Stark\Interfaces\TDG|\Stark\TDG\ReservationTDG
         */
        public function getTdg()
        {
            return $this->_tdg;
        }

        /**
         * Retrieve all reservations under a given userId
         *
         * @param $userid
         *
         * @return array
         */
        public function findAllStudentReservations($userid)
        {
            $dbEntries = $this->getTdg()->findAllStudentReservations($userid);
            $reservations = [];
            foreach ($dbEntries as $row) {
                $reservations[] = $this->getModel($row);
            }

            return $reservations;
        }

        /**
         * The method calculates the number of reservations made for a particular user and a given start time date.
         * @param $date string this is the start time of the reservation.
         * @param $uid int user id
         *
         * @return int number of reservation made
         */
        public function numberOfReservationsMadeWeekUser($date, $uid)
        {
            $reservations = $this->findAll();
            $weekDates = Utilities::getWeek($date); // get week dates based on today's date
            $startDateWeek = $weekDates[0];
            $endDateWeek = $weekDates[1];
            $numberOfReservations = 0;
            /**
             * @var $Reservation Reservation
             */
            foreach ($reservations as $Reservation) {
                // find this user's reservations
                if ($Reservation->getUserId() == $uid) {
                    if (strtotime($Reservation->getStartTimeDate()) >= strtotime($startDateWeek) && strtotime($Reservation->getEndTimeDate()) <= strtotime($endDateWeek)) {
                        $numberOfReservations++;
                    }
                }
            }
            return $numberOfReservations;
        }

        /**
         * Returns all reservations in the system
         *
         * @return array
         */
        public function getReservations()
        {
            $dbEntries = $this->getTdg()->findAll();
            $reservations = [];
            foreach ($dbEntries as $row) {
                $reservations[] = $this->getModel($row);
            }

            return $reservations;
        }

        /**
         * Creates a new Reservation object
         *
         * @param $userId
         * @param $roomId
         * @param $startTime
         * @param $endTime
         * @param $title
         * @param bool $waiting
         *
         * @return \Stark\Models\Reservation
         */
        public function createReservation($userId, $roomId, $startTime, $endTime, $title, $waiting = false)
        {
            $Reservation = new Reservation();
            $Reservation->setUserId($userId);
            $Reservation->setStartTimeDate($startTime);
            $Reservation->setEndTimeDate($endTime);
            $Reservation->setRoomId($roomId);
            $Reservation->setCreatedOn(date("Y-m-d H:i:s"));
            $Reservation->setTitle($title);
            $Reservation->setIsWaited($waiting);
            return $Reservation;
        }

        /**
         * Returns all waitlisted reservations
         *
         * @return array
         */
        public function findAllWaitlisted()
        {
            $dbEntries = $this->getTdg()->findAllWaitlisted();
            $waitlistedReservations = [];
            foreach ($dbEntries as $row) {
                $waitlistedReservations[] = $this->getModel($row);
            }

            return $waitlistedReservations;
        }

        /**
         * Returns all active reservations
         *
         * @return array
         */
        public function findAllActive()
        {
            $dbEntries = $this->getTdg()->findAllActive();
            $activeReservations = [];
            foreach ($dbEntries as $row) {
                $activeReservations[] = $this->getModel($row);
            }

            return $activeReservations;
        }

        /**
         * Creates a Reservation object from a DB entry
         *
         * @param $data array data retrieve from the tdg
         *
         * @return Reservation returns a fully-dressed object
         */
        public function getModel(array $data = null)
        {
            if (!$data) {
                return NULL;
            }
            $Reservation = new Reservation();
            $Reservation->setRoomId($data['RoomId']);
            $Reservation->setStartTimeDate($data["Starttime"]);
            $Reservation->setEndTimeDate($data['Endtime']);
            $Reservation->setReservationID($data['ReservationId']);
            $Reservation->setCreatedOn($data['CreatedOn']);
            $Reservation->setTitle($data['Title']);
            $Reservation->setUserId($data['UserId']);
            $Reservation->setIsWaited($data["Waiting"]);

            return $Reservation;
        }
    }

}