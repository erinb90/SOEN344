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
     * Builds the reservationId of the reservation.
     *
     * @param int $reservationId of the reservation.
     * @return ReservationRequestBuilder for the reservation.
     */
    public function reservationId($reservationId)
    {
        $this->reservationRequest->setReservationId($reservationId);
        return $this;
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
     * Builds the equipment requests for the reservation.
     *
     * @param EquipmentRequest[] $equipmentRequests for the reservation.
     * @return ReservationRequestBuilder for the reservation.
     */
    public function equipmentRequests($equipmentRequests)
    {
        $this->reservationRequest->setEquipmentRequests($equipmentRequests);
        return $this;
    }

    /**
     * Builds the recurrences for the reservation request.
     *
     * @param int $recurrences for the reservation.
     * @return ReservationRequestBuilder for the reservation.
     */
    public function recurrences($recurrences)
    {
        $this->reservationRequest->setRecurrences($recurrences);
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