<?php

/**
 * Created by PhpStorm.
 * User: Server
 * Date: 1/21/2017
 * Time: 3:37 PM
 */
class MakeReservationSession
{

    /**
     * @var \StudentDomain
     */
    private $_User;

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


    /**
     * MakeReservationSession constructor.
     */
    public function __construct()
    {
        //parent::__construct();
    }

    /**
     * @return string
     */
    public function getStartTime()
    {
        return $this->_startTime;
    }

    /**
     * @return string
     */
    public function getEndTime()
    {
        return $this->_endTime;
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

        // validate time slot
        $TimeSlotValidator = new TimeValidator($this->_startTime, $this->_endTime);
        $TimeSlotValidatorErrors = $TimeSlotValidator->getErrors();
        if (!empty($TimeSlotValidatorErrors))
        {
            foreach ($TimeSlotValidatorErrors as $error)
            {
                $this->setError($error);
            }
        }

        //find conflicts
        $ConflictManager = new ConflictManager($this->_startTime, $this->_endTime, $this->_rid, array());
        $this->_conflicts = $ConflictManager->getConflicts();

        // check how many reservations student made and if he's allowed to make any more
        $ReservationMapper = new ReservationMapper();
        $reservationsMade = $ReservationMapper->numberOfReservationsMadeWeekUser($this->_startTime, $this->_User->getSID());

        // find all student's reservations
        $waitingUserRes = $ReservationMapper->findAllStudentReservations($this->_User->getSID());
        if ($reservationsMade >= CoreConfig::settings()['reservations']['max_per_week'])
        {
            $this->setError("Exceeded maximum number of reservations allowed for the week: " . CoreConfig::settings()['reservations']['max_per_week']);
        } //CHECK IF THE USER ALREADY HAS A RESERVATION AT THE SAME TIME BUT DIFFERENT ROOM
        else if (!empty($waitingUserRes))
        {

            /*
            foreach ($waitingUserRes as $res)
            {
                if ($this->_startTime >= $res->getStartTime() && $this->_endTime <= $res->getEndTime())
                {

                    $this->setError("You already have a reservation at the sametime but different room on the same day! HOW COME");
                    break;
                }

                if ($this->_startTime >= $res->getStartTime() && $this->_startTime < $res->getEndTime())
                {

                    $this->setError("You already have a reservation at the sametime but different room on the same day! HOW COME");
                    break;
                }


                if ($this->_endTime > $res->getStartTime() && $this->_endTime <= $res->getEndTime())
                {
                    $this->setError("You already have a reservation at the sametime but different room on the same day! HOW COME");
                    break;
                }

                if ($this->_startTime <= $res->getStartTime() && $this->_endTime >= $res->getEndTime())
                {

                    $this->setError("You already have a reservation at the sametime but different room on the same day! HOW COME");
                    break;
                }


            }
            */
        }



    }



    /**
     * @return bool returns true if the reservation was successful with no time conflicts
     *
     */
    public function reserve(StudentDomain $user, $roomid, $start, $end, $title, $description)
    {
        $this->_User = $user;
        $this->_rid = $roomid;
        $this->_endTime = $end;
        $this->_startTime = ($start);

        $this->_title = $title;
        $this->_description = $description;


        $this->validate();
        // if no errors and no conflicts
        if (empty($this->getErrors()) && empty($this->getConflicts()))
        {

            try
            {

                $ReservationMapper = new ReservationMapper();
                $Reservation = $ReservationMapper->createReservation(
                    $this->_User->getSID(),
                    $this->_rid,
                    $this->_startTime,
                    $this->_endTime,
                    $this->_title,
                    $this->_description);
                $ReservationMapper->uowInsert($Reservation);
                $ReservationMapper->commit();
                return true;

            }
            catch (Exception $e)
            {
                $this->setError($e->getMessage());
            }

        }

        return false;

    }

}