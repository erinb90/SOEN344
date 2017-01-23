<?php

/**
 * Created by PhpStorm.
 * User: Server
 * Date: 1/21/2017
 * Time: 3:37 PM
 */
class CreateReservationSession
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
    private $_startTimeDate;

    /**
     * @var string
     */
    private $_endTimeDate;


    private $_description;

    private $_title;


    private $_errors =array();

    private $_conflicts=array();


    private $_date;
    /**
     * MakeReservationSession constructor.
     */
    public function __construct(StudentDomain $student, $startTimeDate, $endTimeDate)
    {
        $this->_User = $student;
        $this->_startTimeDate = $startTimeDate;
        $this->_endTimeDate = $endTimeDate;
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

        //find conflicts
        $ConflictManager = new ConflictManager($this->_startTimeDate, $this->_endTimeDate, $this->_rid, array());
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


        if($this->_title == "")
        {
            $this->setError("Title must be provided");
        }

    }

    /**
     * @return array
     */
    public function getConflicts()
    {
        return $this->_conflicts;
    }


    /**
     * @return bool returns true if the reservation was successful with no time conflicts
     *
     */
    public function reserve()
    {

        $this->validate();
        // if no errors and no conflicts
        if (empty($this->getErrors()) && empty($this->getConflicts()))
        {

            try
            {

                $ReservationMapper = new ReservationMapper();
                //todo: instead of having the mapper create the ReservationDomain object, create the object in this class and pass it to UOW
                $Reservation = $ReservationMapper->createReservation(
                    $this->_User->getSID(),
                    $this->_rid,
                    $this->_startTimeDate,
                    $this->_endTimeDate,
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