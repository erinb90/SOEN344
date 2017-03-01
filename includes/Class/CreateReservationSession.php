<?php
namespace Stark
{

    use Stark\Models\Reservation;

    use Stark\Mappers\ReservationMapper;
    use Stark\Models\User;

    class CreateReservationSession
    {

        /**
         * @var User
         */
        private $_User;

        /**
         * @var int
         */
        private $_rid;

        /**
         * @var string
         */
        private $_startTimeDate;
        /**
         * @var string
         */
        private $_endTimeDate;


        private $_title;


        private $_errors    = [];

        private $_repeats = 0;


        /**
         * CreateReservationSession constructor.
         *
         * @param \Stark\Models\User $student
         * @param $startTimeDate
         * @param $endTimeDate
         * @param int $repeats number of repeats
         */
        public function __construct(User $student, $startTimeDate, $endTimeDate, $repeats = 0)
        {
            $this->_User = $student;
            $this->_startTimeDate = $startTimeDate;
            $this->_endTimeDate = $endTimeDate;
            $this->_repeats = 0;
        }


        public function getErrors()
        {
            return $this->_errors;
        }

        public function setError($error)
        {
            $this->_errors[] = $error;
        }


        /**
         * Performs certain validation according to requirements
         */
        public function validate()
        {
            // the 3 should be from requirements
            $repeatedDates = Utilities::getDateRepeats($this->_startTimeDate, $this->_endTimeDate, 1);
            foreach($repeatedDates as $date)
            {
                // do some checks

                // are the conflicts? etc etc etc
            }

        }



        /**
         * @return bool returns true if the reservation was successful with no time conflicts
         *
         */
        public function reserve()
        {

            $this->validate();
            // if no errors and no conflicts
            if (empty($this->getErrors()))
            {

                $repeatedDates = Utilities::getDateRepeats($this->_startTimeDate, $this->_endTimeDate, 1);

                // create a repeated reservation based on the date repeats above
                foreach($repeatedDates as $i => $date)
                {
                    try
                    {

                        $ReservationMapper = new ReservationMapper();
                        /**
                         * @var $Reservation Reservation
                         */
                        $Reservation = $ReservationMapper->createReservation(
                            $this->_User->getUserId(),
                            $this->_rid,
                            $date["start"],
                            $date["end"],
                            $this->_title);
                        // add it to the unit of work
                        $ReservationMapper->uowInsert($Reservation);
                        // commit to unit of work
                        $ReservationMapper->commit();

                        return TRUE;

                    }
                    catch (\Exception $e)
                    {
                        $this->setError($e->getMessage());
                    }
                }



            }

            return FALSE;

        }

    }
}