<?php

namespace Stark\Utilities;

use Stark\Models\LoanedEquipment;
use Stark\Models\Reservation;

class ReservationConflict
{
    /**
     * @var Reservation $_reservation that is causing the conflict.
     */
    private $_reservation;

    /**
     * @var LoanedEquipment[] $_equipments that are causing conflicts (optional).
     */
    private $_equipments;

    /**
     * @var String[] $_dateTimes that are causing conflicts with the reservation.
     */
    private $_dateTimes;

    /**
     * ReservationConflict constructor.
     *
     * @param Reservation $reservation that is causing the conflict
     */
    public function __construct($reservation)
    {
        $this->_reservation = $reservation;
        $this->_equipments = [];
        $this->_dateTimes = [];
    }

    /**
     * @return Reservation that is causing the conflict
     */
    public function getReservation()
    {
        return $this->_reservation;
    }

    /**
     * @return LoanedEquipment[] that are causing conflicts
     */
    public function getEquipments()
    {
        return $this->_equipments;
    }

    /**
     * @param LoanedEquipment $equipment that is causing a conflict
     *
     * @return void
     */
    public function addEquipment($equipment)
    {
        $this->_equipments[] =  $equipment;
    }

    /**
     * @return String[] date times that are causing conflicts
     */
    public function getDateTimes()
    {
        return $this->_dateTimes;
    }

    /**
     * @param String[] $dateTime that is causing a conflict
     *
     * @return void
     */
    public function addDateTime($dateTime)
    {
        $this->_dateTimes[] = $dateTime;
    }
}