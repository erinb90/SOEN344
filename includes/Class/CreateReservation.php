<?php

/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-01-21
 * Time: 9:51 PM
 */
class CreateReservation
{

    /**
     * @var \StudentDomain
     */
    private $_student;

    /**
     * @var int
     */
    private $_rid;

    /**
     * @var string
     */
    private $_startTime;

    /**
     * @var string
     */
    private $_endTime;


    private $_description;

    private $_title;


    private $_errors =array();

    private $_conflicts=array();


    private $_date;


    private $_startTimeDate;

    private $_endTimeDate;

    private $_repeatDates = array();


    private $_repeats = 0 ;
    public function __construct($user, $roomid, $title, $description, $date, $startTime, $endTime, $repeats=  0)
    {
        $this->_student = $user;
        $this->_rid = $roomid;
        $this->_title = $title;
        $this->_description = $description;
        $this->_startTime = $startTime . ':00';
        $this->_endTime = $endTime . ':00';
        $this->_date = $date;
        $this->_repeats = $repeats;


        $this->_startTimeDate = $this->_date . ' ' . $this->_startTime;
        $this->_endTimeDate = $this->_date .' '. $this->_endTime;

        $this->findRepeatDates();


    }


    public function setError($error)
    {
        $this->_errors[] = $error;
    }


    private function validate()
    {
        if($this->_date == "")
        {
            $this->setError("Date required");
            return;
        }


        // validate time slot
        $TimeSlotValidator = new TimeValidator($this->_startTimeDate, $this->_endTimeDate);
        $TimeSlotValidatorErrors = $TimeSlotValidator->getErrors();
        if (!empty($TimeSlotValidatorErrors))
        {
            foreach ($TimeSlotValidatorErrors as $error)
            {
                $this->setError($error);
            }
        }

        // check how many reservations the student made for particular weeks
        foreach($this->_repeatDates as $date)
        {
            $ReservationMapper = new ReservationMapper();
            $weekReservationsMade = $ReservationMapper->numberOfReservationsMadeWeekUser($date, $this->_student->getSID()) + 4;

            echo $weekReservationsMade;
            if($weekReservationsMade >= CoreConfig::settings()['reservations']['max_per_week'])
            {
                $this->setError("Cannot exceed a max quota of " . CoreConfig::settings()['reservations']['max_per_week'] ."  reservations for the week of " . $date);
            }
        }
    }

    private function findRepeatDates()
    {
        $dates = array();
        for($i = 0; $i <= $this->_repeats; $i++)
        {
            $days = $i * 7;
            $dates[] = date('Y-m-d', strtotime($this->_date. ' + ' . $days . ' days'));
        }

        $this->_repeatDates = $dates;
    }

    private function getRepeatDates()
    {
        return $this->_repeatDates;
    }

    public function addConflict($conflict)
    {
        $this->_conflicts[] = $conflict;
    }

    public function getConflicts()
    {
        return $this->_conflicts;
    }


    private function findConflicts()
    {
        $conflicts = array();
        foreach ($this->getRepeatDates() as $startDate)
        {
            $startTimeDate = $startDate . ' ' . $this->_startTime ;
            $endTimeDate = $startDate . ' ' . $this->_endTime ;


            $ConflictManager = new ConflictManager($startTimeDate, $endTimeDate, $this->_rid, array());

            if(count($ConflictManager->getConflicts()) > 0)
            {
                foreach($ConflictManager->getConflicts() as $conflict)
                {

                    $conflicts[] = $conflict;
                }
            }
       }

       foreach($conflicts as  $i => $conflict)
       {
           $this->_conflicts[] = $conflict;
       }


    }

    public function reserve()
    {
        $this->validate();
        $this->findConflicts();

        if(empty($this->_errors) && empty($this->_conflicts))
        {
            try
            {
                foreach ($this->_repeatDates as $startDate)
                {
                    $startTimeDate = $startDate . ' ' . $this->_startTime ;
                    $endTimeDate = $startDate . ' ' . $this->_endTime;


                    $ReservationMapper = new ReservationMapper();
                    $Reservation = $ReservationMapper->createReservation(
                        $this->_student->getSID(),
                        $this->_rid,
                        $startTimeDate,
                        $endTimeDate,
                        $this->_title,
                        $this->_description);
                    $ReservationMapper->uowInsert($Reservation);
                    $ReservationMapper->commit();
                }
            }
            catch (Exception $e)
            {
                $this->setError($e->getMessage());
                return false;
            }

        }
        else
        {
            return false;
        }

        return true;


    }

    public function getErrors()
    {
        return $this->_errors;
    }

}