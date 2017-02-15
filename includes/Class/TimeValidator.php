<?php
namespace Stark;
use Utilities;

/**
 * Created by PhpStorm.
 * User: Server
 * Date: 1/21/2017
 * Time: 3:49 PM
 */
class TimeValidator
{
    private $_startTime;



    private $_endTime;

    private $_errors = array();

    /**
     * TimeSlotValidator constructor.
     *
     * @param $start string start time
     * @param $end string end time
     */
    public function __construct($start, $end)
    {
        $this->_startTime = trim($start);
        $this->_endTime = trim($end);
        $this->validate();
    }

    /**
     * @param $error
     */
    public function setError($error)
    {
        $this->_errors[] = $error;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     *
     */
    private function validate()
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
}