<?php
namespace Stark;
use Stark\Mappers\ReservationMapper;

/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 1/21/2017
 * Time: 3:56 PM
 */
class ConflictManager
{

    private $_startTime;

    private $_endTime;

    private $_roomId;

    private $_ignored = array();

    private $_conflicts= array();

    public function __construct($startTime, $endTime, $roomId, array $ignoreReservationIds )
    {
        $this->_startTime = $startTime;
        $this->_endTime = $endTime;
        $this->_roomId = $roomId;
        $this->_ignored = $ignoreReservationIds;


        $this->findConflicts();
    }

    private function findConflicts()
    {
        $ReservationMapper = new ReservationMapper();
        $reservations = $ReservationMapper->getAllReservations();
        $start = strtotime($this->_startTime);
        $end = strtotime($this->_endTime);

        $conflicts = array();
        /**
         * @var $Reservation \Stark\Models\ReservationDomain
         */
        foreach ($reservations as $Reservation)
        {
            if (count($this->_ignored) > 0 && in_array($Reservation->getReID(), $this->_ignored))
            {
                continue;
            }

            $startTime = strtotime($Reservation->getStartTimeDate());
            $endTime = strtotime($Reservation->getEndTimeDate());
            $rid = $Reservation->getRID();


            if ($rid == $this->_roomId)
            {
                // was the start time of current reservation between the conflicted start and end time period?
                if ($start >= $startTime && $start < $endTime)
                {
                    $conflicts[$Reservation->getReID()] = $Reservation;
                }
                // was the end time of current reservation between the conflicted start and end time period?
                if ($end > $startTime && $end <= $endTime)
                {
                    $conflicts[$Reservation->getReID()] = $Reservation;
                }
                // is the current reservation start time less than the one reserved and is the end time of the one reserved less than the current end time
                if ($startTime >= $start && $end >= $endTime)
                {
                    $conflicts[$Reservation->getReID()] = $Reservation;
                }

            }
        }

        $this->_conflicts = $conflicts;
    }


    public function getConflicts()
    {
        return $this->_conflicts;
    }
}