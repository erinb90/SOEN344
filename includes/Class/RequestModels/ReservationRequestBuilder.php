<?php

namespace Stark\RequestModels;
/**
 * Class ReservationRequestBuilder
 * @package Stark\RequestModels
 */
class ReservationRequestBuilder implements Builder
{
    /**
     * @var ReservationRequest $reservationRequest to build.
     */
    private $reservationRequest;

    /**
     * ReservationRequestBuilder constructor.
     */
    public function __construct()
    {
        $this->reservationRequest = new ReservationRequest();
    }

    /**
     * Builds the title of the reservation.
     *
     * @param string $title of the reservation.
     * @return ReservationRequestBuilder for the reservation.
     */
    public function title($title)
    {
        $this->reservationRequest->setTitle($title);
        return $this;
    }

    /**
     * Builds the roomId of the reservation.
     *
     * @param int $roomId of the reservation.
     * @return ReservationRequestBuilder for the reservation.
     */
    public function roomId($roomId)
    {
        $this->reservationRequest->setRoomId($roomId);
        return $this;
    }

    /**
     * Builds the userId of the reservation.
     *
     * @param int $userId of the reservation.
     * @return ReservationRequestBuilder for the reservation.
     */
    public function userId($userId)
    {
        $this->reservationRequest->setUserId($userId);
        return $this;
    }

    /**
     * Builds the start time date of the reservation.
     *
     * @param string $startTimeDate of the reservation.
     * @return ReservationRequestBuilder for the reservation.
     */
    public function startTimeDate($startTimeDate)
    {
        $this->reservationRequest->setStartTimeDate($startTimeDate);
        return $this;
    }

    /**
     * Builds the start time date of the reservation.
     *
     * @param string $endTimeDate of the reservation.
     * @return ReservationRequestBuilder for the reservation.
     */
    public function endTimeDate($endTimeDate)
    {
        $this->reservationRequest->setEndTimeDate($endTimeDate);
        return $this;
    }

    /**
     * @return ReservationRequest
     */
    public function build()
    {
        return $this->reservationRequest;
    }
}