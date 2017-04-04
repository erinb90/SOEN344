<?php
namespace Stark
{



    /**
     * Class TimeValidator
     * @package Stark
     */
    class TimeValidator
    {

        /**
         * @var string
         */
        private $_startTime;

        /**
         * @var string
         */
        private $_endTime;

        /**
         * @var array
         */
        private $_errors = [];

        /**
         * TimeSlotValidator constructor.
         *
         * @param $start string start time
         * @param $end   string end time
         */
        private function __construct($start, $end)
        {
            $this->_startTime = trim($start);
            $this->_endTime = trim($end);
            $this->check();
        }

        /**
         * @param $error
         */
        public function setError($error)
        {
            $this->_errors[] = $error;
        }

        /**
         * @return array returns an array of errors
         */
        public function getErrors()
        {
            return $this->_errors;
        }

        /**
         * Perform check
         */
        private function check()
        {

            // Are the dates even valid?
            if (!Utilities::validateDate($this->_startTime, "Y-m-d H:i:s"))
            {
                $this->setError("Start time not valid");
            }
            if (!Utilities::validateDate($this->_endTime))
            {
                $this->setError("End time not valid");
            }

            $minutes = (strtotime($this->_endTime) - strtotime($this->_startTime)) / 60;

            if (strtotime($this->_startTime) < strtotime(date("Y-m-d H:i:s")) || strtotime($this->_endTime) < strtotime(date("Y-m-d H:i:s")))
            {
                $this->setError("Start date and end date cannot be set in the past");
            }
            else if (isset($this->_startTime) && isset($this->_endTime))
            {
                if (strtotime($this->_startTime) > strtotime($this->_endTime))
                {
                    $this->setError("Start time cannot be bigger than end time");
                }
                else if (strtotime($this->_endTime) < strtotime($this->_startTime))
                {
                    $this->setError("End time cannot be less than start time");
                }
                else if ($minutes > CoreConfig::settings()['reservations']['max_per_reservation'])
                {
                    $this->setError("Reservation cannot be made for more than " . CoreConfig::settings()['reservations']['max_per_reservation'] . " minutes");
                }
            }
            else
            {
                $this->setError("Start times and end times required");
            }
        }

        /**
         * @param $start string start time of the timeslot as (Y-M-D H:i:s)
         * @param $end string end time of the timeslot as (Y-M-D H:i:s)
         *
         * @return \Stark\TimeValidator
         */
        public static function validate($start, $end)
        {
            return new TimeValidator($start,$end);
        }
    }
}