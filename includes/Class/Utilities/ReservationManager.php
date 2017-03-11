<?php

namespace Stark\Utilities;

use Stark\Interfaces\Equipment;
use Stark\Mappers\ReservationMapper;
use Stark\Models\EquipmentRequest;
use Stark\Models\Reservation;

class ReservationManager
{
    /**
     * @var \Stark\Mappers\ReservationMapper $_reservationMapper to retrieve reservations
     */
    private $_reservationMapper;

    /**
     * @var \Stark\Utilities\EquipmentManager $_equipmentManager to manage equipment
     */
    private $_equipmentManager;

    /**
     * WaitlistManager constructor.
     */
    public function __construct()
    {
        $this->_reservationMapper = new ReservationMapper();
        $this->_equipmentManager = new EquipmentManager();
    }

    /**
     * Gets waitlisted reservations.
     *
     * @return array of waitlisted reservations or empty if none
     */
    public function getWaitlistedReservations()
    {
        return $this->_reservationMapper->findAllWaitlisted();
    }

    /**
     * Find conflicting active reservations based on an existing reservation using Id.
     *
     * @param int $reservationId of the reservation that is being checked
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    public function checkForExistingConflictsWithId($reservationId)
    {
        $reservation = $this->findReservationForId($reservationId);
        if ($reservation == null) {
            return [];
        }
        $reservations = $this->_reservationMapper->findAll();
        /**
         * @var Equipment[] $equipmentsForReservation
         */
        $equipments = $this->_equipmentManager->findEquipmentForReservation($reservation->getReservationID());
        $equipmentRequests = [];
        foreach ($equipments as $equipment) {
            $equipmentRequests[] = new EquipmentRequest($equipment->getEquipmentId(), $equipment->getDiscriminator());
        }
        return $this->checkForActiveReservations($reservation->getRoomId(), $reservation->getStartTimeDate(), $reservation->getEndTimeDate(), $reservations, $equipmentRequests);
    }

    /**
     * Find conflicting active reservations based on an existing reservation.
     *
     * @param Reservation $reservation that is being checked
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    public function checkForExistingConflicts($reservation)
    {
        $reservations = $this->_reservationMapper->findAll();
        /**
         * @var Equipment[] $equipmentsForReservation
         */
        $equipments = $this->_equipmentManager->findEquipmentForReservation($reservation->getReservationID());
        $equipmentRequests = [];
        foreach ($equipments as $equipment) {
            $equipmentRequests[] = new EquipmentRequest($equipment->getEquipmentId(), $equipment->getDiscriminator());
        }
        return $this->checkForActiveReservations($reservation->getRoomId(), $reservation->getStartTimeDate(), $reservation->getEndTimeDate(), $reservations, $equipmentRequests);
    }

    /**
     * Find conflicting active reservations based on a yet to be created reservation.
     *
     * @param int $roomId of the room in the pending reservation
     * @param \DateTime $startTimeDate of the pendingReservation
     * @param \DateTime $endTimeDate of the pendingReservation
     * @param EquipmentRequest[] $equipmentRequests of the equipment requested (optional)
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    public function checkForConflicts($roomId, $startTimeDate, $endTimeDate, $equipmentRequests = [])
    {
        $reservations = $this->_reservationMapper->findAll();
        return $this->checkForActiveReservations($roomId, $startTimeDate, $endTimeDate, $reservations, $equipmentRequests);
    }

    /**
     * Filter active reservations in order to check conflicts for a pending reservation.
     *
     * @param int $roomId of the room in the pending reservation
     * @param \DateTime $startTimeDate of the pendingReservation
     * @param \DateTime $endTimeDate of the pendingReservation
     * @param Reservation[] $reservations in the system
     * @param EquipmentRequest[] $equipmentRequests of the equipment requested (optional)
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    private function checkForActiveReservations($roomId, $startTimeDate, $endTimeDate, $reservations, $equipmentRequests)
    {
        if (empty($reservations) || !isset($roomId) || !isset($startTimeDate) || !isset($endTimeDate)) {
            return [];
        }

        $activeReservations = [];
        foreach ($reservations as $reservation) {

            // Filter out active reservations
            if (!$reservation->isIsWaited()) {
                $activeReservations[] = $reservation;
            }
        }

        if (empty($activeReservations)) {
            return [];
        }

        return $this->checkForTimeConflicts($roomId, $startTimeDate, $endTimeDate, $activeReservations, $equipmentRequests);
    }

    /**
     * Find reservation for Id.
     *
     * @param int $reservationId for the reservation
     *
     * @return Reservation matching the reservationId or null if none
     */
    private function findReservationForId($reservationId)
    {
        // TODO : Cast to reservation model
        return $this->_reservationMapper->findByPk($reservationId);
    }

    /**
     * Find conflicting active reservations based on startTimeDate and endTimeDate of the pending reservation.
     *
     * @param int $roomId of the room in the pending reservation
     * @param \DateTime $startTimeDate of the pendingReservation
     * @param \DateTime $endTimeDate of the pendingReservation
     * @param Reservation[] $activeReservations in the system
     * @param EquipmentRequest[] $equipmentRequests of the equipment requested (optional)
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    private function checkForTimeConflicts($roomId, $startTimeDate, $endTimeDate, $activeReservations, $equipmentRequests)
    {
        if (empty($activeReservations) || !isset($roomId) || !isset($startTimeDate) || !isset($endTimeDate)) {
            return [];
        }

        $hasEquipment = !empty($equipmentRequests);
        $conflictingReservations = [];

        foreach ($activeReservations as $activeReservation) {
            $reservationConflict = new ReservationConflict($activeReservation);

            // Is the start of the current reservation contained between the start and end time of an active one?
            if ($startTimeDate >= $activeReservation->getStartTimeDate()
                && $startTimeDate <= $activeReservation->getEndTimeDate()
            ) {
                if ($roomId == $activeReservation->getRoomId()) {
                    $reservationConflict->addReasonForConflict("Overlapping start time.");
                }

                if ($hasEquipment) {
                    $this->checkForEquipmentConflicts($equipmentRequests, $activeReservation, $reservationConflict);
                }
            } // Is the end of the current reservation contained between the start and end time of an active one?
            else if ($endTimeDate >= $activeReservation->getStartTimeDate()
                && $endTimeDate <= $activeReservation->getEndTimeDate()
            ) {
                if ($roomId == $activeReservation->getRoomId()) {
                    $reservationConflict->addReasonForConflict("Overlapping end time.");
                }

                if ($hasEquipment) {
                    $this->checkForEquipmentConflicts($equipmentRequests, $activeReservation, $reservationConflict);
                }
            } // Does the current reservation contain the start and end of an active one?
            else if ($startTimeDate <= $activeReservation->getStartTimeDate()
                && $endTimeDate >= $activeReservation->getEndTimeDate()
            ) {
                if ($roomId == $activeReservation->getRoomId()) {
                    $reservationConflict->addReasonForConflict("Overlapping entire reservation time.");
                }

                if ($hasEquipment) {
                    $this->checkForEquipmentConflicts($equipmentRequests, $activeReservation, $reservationConflict);
                }
            }

            $conflictingReservations[] = $reservationConflict;
        }

        return $conflictingReservations;
    }

    /**
     * Find conflicting equipment requests for a pending reservation.
     *
     * @param EquipmentRequest[] $equipmentRequests that are being requested.
     * @param Reservation $activeReservation in the system
     * @param ReservationConflict &$reservationConflict with the active reservation
     *
     * @return void of conflicting reservations or empty if none
     */
    private function checkForEquipmentConflicts($equipmentRequests, $activeReservation, &$reservationConflict)
    {
        if (empty($equipmentRequests) || !isset($activeReservation)) {
            return;
        }

        $equipmentsForActiveReservation = $this->_equipmentManager->findEquipmentForReservation($activeReservation->getReservationID());
        if (empty($equipmentsForActiveReservation)) {
            return;
        }

        // TODO : Refactor for better efficiency
        foreach ($equipmentRequests as $equipmentRequest) {
            foreach ($equipmentsForActiveReservation as $equipmentForActiveReservation) {
                if ($equipmentRequest->getEquipmentId() == $equipmentForActiveReservation->getEquipmentId()) {
                    $reservationConflict->addReasonForConflict("Conflict with equipment Id: " . $equipmentRequest->getEquipmentId());
                }
            }
        }
    }
}