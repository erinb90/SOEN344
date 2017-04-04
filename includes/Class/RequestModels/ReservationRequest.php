<?php
namespace Stark\RequestModels;

use Stark\CoreConfig;

/**
 * Class ReservationRequest
 * @package Stark\RequestModels
 */
class ReservationRequest
{
    /**
     * Default value if a reservation Id has not yet been assigned.
     */
    const NO_RESERVATION_ID = -1;

    /**
     * @var int $maxRecurrences for a reservation.
     */
    private $maxRecurrences;

    /**
     * @var int $reservationId of an existing reservation or -1 if new.
     */
    private $reservationId;

    /**
     * @var string $title of the reservation.
     */
    private $title;

    /**
     * @var int $roomId of the reservation.
     */
    private $roomId;

    /**
     * @var int $userId of the user.
     */
    private $userId;

    /**
     * @var string $startTimeDate of the reservation.
     */
    private $startTimeDate;

    /**
     * @var string $endTimeDate of the reservation.
     */
    private $endTimeDate;

    /**
     * @var int $recurrences of the reservation.
     */
    private $recurrences;

    /**
     * @var EquipmentRequest[] $equipmentRequests for the reservation.
     */
    private $equipmentRequests;

    /**
     * ReservationRequest constructor.
     */
    public function __construct()
    {
        $this->reservationId = self::NO_RESERVATION_ID;
        $this->maxRecurrences = CoreConfig::settings()['reservations']['max_repeats'];
    }

    /**
     * Gets the max recurrences for a single reservation.
     *
     * @return int max recurrences for a single reservation.
     */
    public function getMaxRecurrences()
    {
        return $this->maxRecurrences;
    }

    /**
     * Gets the reservationId of the reservation.
     *
     * @return int id of the reservation.
     */
    public function getReservationId()
    {
        return $this->reservationId;
    }

    /**
     * Sets the reservationId of the reservation.
     *
     * @param int $reservationId of the reservation.
     */
    public function setReservationId($reservationId)
    {
        $this->reservationId = $reservationId;
    }

    /**
     * Gets the title of the reservation.
     *
     * @return string title of the reservation.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title of the reservation.
     *
     * @param string $title of the reservation.
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Gets the roomId for the reservation.
     *
     * @return int roomId of the reservation.
     */
    public function getRoomId()
    {
        return $this->roomId;
    }

    /**
     * Sets the roomID for the reservation.
     *
     * @param int $roomId of the reservation..
     * @return void
     */
    public function setRoomId($roomId)
    {
        $this->roomId = $roomId;
    }

    /**
     * Gets the userId for the reservation.
     *
     * @return int userId of the user.
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets the userId for the reservation.
     *
     * @param int $userId of the user.
     * @return void
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Gets the start time date for the reservation.
     *
     * @return string start time date for the reservation.
     */
    public function getStartTimeDate()
    {
        return $this->startTimeDate;
    }

    /**
     * Sets the start time date for the reservation.
     *
     * @param string $startTimeDate for the reservation.
     */
    public function setStartTimeDate($startTimeDate)
    {
        $this->startTimeDate = $startTimeDate;
    }

    /**
     * Gets the end time date for the reservation.
     *
     * @return string end time date for the reservation.
     */
    public function getEndTimeDate()
    {
        return $this->endTimeDate;
    }

    /**
     * Sets the end time date for the reservation.
     *
     * @param string $endTimeDate for the reservation.
     */
    public function setEndTimeDate($endTimeDate)
    {
        $this->endTimeDate = $endTimeDate;
    }

    /**
     * Gets the recurrences of the reservation.
     *
     * @return int recurrences of the reservation.
     */
    public function getRecurrences()
    {
        return $this->recurrences;
    }

    /**
     * Sets the recurrences of the reservation.
     *
     * @param int $recurrences of the reservation.
     */
    public function setRecurrences($recurrences)
    {
        $this->recurrences = $recurrences;
    }

    /**
     * Gets the equipment requests for the reservation.
     *
     * @return EquipmentRequest[] for the reservation.
     */
    public function getEquipmentRequests()
    {
        return $this->equipmentRequests;
    }

    /**
     * Sets the equipment requests for the reservation.
     *
     * @param EquipmentRequest[] $equipmentRequests for the reservation.
     */
    public function setEquipmentRequests($equipmentRequests)
    {
        $this->equipmentRequests = $equipmentRequests;
    }
}