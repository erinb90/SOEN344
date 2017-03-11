<?php

namespace Stark\Utilities;

use Stark\Models\Reservation;

class ReservationConflict
{
    /**
     * @var Reservation $_reservation that is causing the conflict
     */
    private $_reservation;

    /**
     * @var String[] $_reasonForConflict
     */
    private $_reasonsForConflict;

    /**
     * ReservationConflict constructor.
     *
     * @param Reservation $reservation that is causing the conflict
     */
    public function __construct($reservation)
    {
        $this->_reservation = $reservation;
        $this->_reasonsForConflict = [];
    }

    /**
     * @return Reservation that is causing the conflict
     */
    public function getReservation()
    {
        return $this->_reservation;
    }

    /**
     * @return String[]
     */
    public function getReasonsForConflict()
    {
        return $this->_reasonsForConflict;
    }

    /**
     * Adds a reason for conflict.
     *
     * @param string $reasonForConflict
     *
     * @return void
     */
    public function addReasonForConflict($reasonForConflict)
    {
        $this->_reasonsForConflict[] = $reasonForConflict;
    }
}