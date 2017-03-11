<?php
/**
 * Created by PhpStorm.
 * User: Erin
 * Date: 3/10/2017
 * Time: 3:54 PM
 */

namespace Stark;

use Stark\Models\Reservation;
use Stark\Mappers\ReservationMapper;

class ModifyReservationSession
{
    /**
     * @var int
     */
    private $_reservationId;

    /**
     * @var String
     */
    private $_newStartTime;

    /**
     * @var  String
     */
    private $_newEndTime;

    /**
     * @var int
     */
    private $_newRoomId;

    /**
     * @var String
     */
    private $_newTitle;

    /**
     * @var ReservationMapper
     */
    private $_ReservationMapper;

    /**
     * ModifyReservationSession constructor.
     * @param $reservationId
     */
    private function __construct($reservationId)
    {
        $this->_reservationId = $reservationId;
        $this->_ReservationMapper = new ReservationMapper();
    }

    public function getReservation()
    {
        /**
         * @var $Reservation Reservation
         */
        $Reservation = $this->_ReservationMapper->findByPk($this->_reservationId);

        return $Reservation;
    }

    /**
     * @return \Stark\Mappers\ReservationMapper
     */
    public function getReservationMapper()
    {
        return $this->_ReservationMapper;
    }

    public static function modify($reservationId, $newStartTime, $newEndTime, $newTitle)
    {
        //modify reservation logic goes here
    }

    public static function validate()
    {
        //perform validations
    }

}