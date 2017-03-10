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
     * @var string $_reasonForConflict
     */
    private $_reasonForConflict;

    /**
     * ReservationConflict constructor.
     *
     * @param Reservation $reservation that is causing the conflict
     * @param string $reasonForConflict
     */
    public function __construct($reservation, $reasonForConflict)
    {
        $this->_reservation = $reservation;
        $this->_reasonForConflict = $reasonForConflict;
    }

    /**
     * @return Reservation that is causing the conflict
     */
    public function getReservation()
    {
        return $this->_reservation;
    }

    /**
     * @return string
     */
    public function getReasonForConflict()
    {
        return $this->_reasonForConflict;
    }
}