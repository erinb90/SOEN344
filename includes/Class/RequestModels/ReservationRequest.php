<?php
namespace Stark\RequestModels;
/**
 * Class ReservationRequest
 * @package Stark\RequestModels
 */
class ReservationRequest
{
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
     * ReservationRequest constructor.
     */
    public function __construct()
    {
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
        return $this->userId;
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
}