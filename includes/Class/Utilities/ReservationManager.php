<?php

namespace Stark\Utilities;

use Stark\Interfaces\Equipment;
use Stark\Mappers\ReservationMapper;
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
     * Find conflicting active reservations based on an existing reservation.
     *
     * @param int $reservationId of the reservation that is being checked
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    public function checkForConflictsWithId($reservationId)
    {
        $reservations = $this->_reservationMapper->findAll();
        $currentReservation = $this->findReservationForId($reservationId);
        return $this->checkForActiveReservations($currentReservation, $reservations);
    }

    /**
     * Find conflicting active reservations based on startTimeDate and endTimeDate.
     *
     * @param Reservation $reservation that is being checked
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    public function checkForConflicts($reservation)
    {
        $reservations = $this->_reservationMapper->findAll();
        return $this->checkForActiveReservations($reservation, $reservations);
    }

    /**
     * Find conflicting active reservations based on a yet to be created reservation.
     *
     * @param int $roomId of the room in the pending reservation
     * @param \DateTime $startTimeDate of the pendingReservation
     * @param \DateTime $endTimeDate of the pendingReservation
     * @param array $equipmentIds of the equipment requested (optional)
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    public function checkForConflictsPendingReservation($roomId, $startTimeDate, $endTimeDate, $equipmentIds = [])
    {
        $reservations = $this->_reservationMapper->findAll();
        return $this->checkForActiveReservationsPendingReservation($roomId, $startTimeDate, $endTimeDate, $reservations, $equipmentIds);
    }

    /**
     * Filter active reservations in order to check conflicts.
     *
     * @param Reservation $currentReservation that is being checked
     * @param Reservation[] $reservations in the system
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    private function checkForActiveReservations($currentReservation, $reservations)
    {
        if (!isset($reservations) || !isset($currentReservation) || empty($reservations)) {
            return [];
        }

        $activeReservations = [];
        foreach ($reservations as $reservation) {

            // Filter out active reservations
            if (!$reservation->isIsWaited()) {
                array_push($activeReservations, $reservation);
            }
        }

        if (empty($activeReservations)) {
            return [];
        }

        return $this->checkForTimeConflicts($currentReservation, $activeReservations);
    }

    /**
     * Filter active reservations in order to check conflicts for a pending reservation.
     *
     * @param int $roomId of the room in the pending reservation
     * @param \DateTime $startTimeDate of the pendingReservation
     * @param \DateTime $endTimeDate of the pendingReservation
     * @param Reservation[] $reservations in the system
     * @param array $equipmentIds of the equipment requested (optional)
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    private function checkForActiveReservationsPendingReservation($roomId, $startTimeDate, $endTimeDate, $reservations, $equipmentIds)
    {
        if (empty($reservations) || !isset($roomId) || !isset($startTimeDate) || !isset($endTimeDate)) {
            return [];
        }

        $activeReservations = [];
        foreach ($reservations as $reservation) {

            // Filter out active reservations
            if (!$reservation->isIsWaited()) {
                array_push($activeReservations, $reservation);
            }
        }

        if (empty($activeReservations)) {
            return [];
        }

        return $this->checkForTimeConflictsPendingReservation($roomId, $startTimeDate, $endTimeDate, $activeReservations, $equipmentIds);
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
     * @param array $equipmentIds of the equipment requested (optional)
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    private function checkForTimeConflictsPendingReservation($roomId, $startTimeDate, $endTimeDate, $activeReservations, $equipmentIds)
    {
        if (empty($activeReservations) || !isset($roomId) || !isset($startTimeDate) || !isset($endTimeDate)) {
            return [];
        }

        $hasEquipment = !empty($equipmentIds);

        $conflictingReservations = [];
        foreach ($activeReservations as $activeReservation) {

            // Is the start of the current reservation contained between the start and end time of an active one?
            if ($startTimeDate >= $activeReservation->getStartTimeDate()
                && $startTimeDate <= $activeReservation->getEndTimeDate()
            ) {
                if ($roomId == $activeReservation->getRoomId()) {
                    $reservationConflict = new ReservationConflict($activeReservation, "Overlapping start time.");
                    array_push($conflictingReservations, $reservationConflict);
                }

                if ($hasEquipment) {
                    $conflictingEquipmentReservations = $this->checkForEquipmentConflictsPendingReservation($equipmentIds, $activeReservation);
                    if (!empty($conflictingEquipmentReservations)) {
                        foreach ($conflictingEquipmentReservations as $conflictingEquipmentReservation){
                            array_push($conflictingReservations, $conflictingEquipmentReservation);
                        }
                    }
                }
            } // Is the end of the current reservation contained between the start and end time of an active one?
            else if ($endTimeDate >= $activeReservation->getStartTimeDate()
                && $endTimeDate <= $activeReservation->getEndTimeDate()
            ) {
                if ($roomId == $activeReservation->getRoomId()) {
                    $reservationConflict = new ReservationConflict($activeReservation, "Overlapping end time.");
                    array_push($conflictingReservations, $reservationConflict);
                }

                if ($hasEquipment) {
                    $conflictingEquipmentReservations = $this->checkForEquipmentConflictsPendingReservation($equipmentIds, $activeReservation);
                    if (!empty($conflictingEquipmentReservations)) {
                        foreach ($conflictingEquipmentReservations as $conflictingEquipmentReservation){
                            array_push($conflictingReservations, $conflictingEquipmentReservation);
                        }
                    }
                }
            } // Does the current reservation contain the start and end of an active one?
            else if ($startTimeDate <= $activeReservation->getStartTimeDate()
                && $endTimeDate >= $activeReservation->getEndTimeDate()
            ) {
                if ($roomId == $activeReservation->getRoomId()) {
                    $reservationConflict = new ReservationConflict($activeReservation, "Overlapping reservation times.");
                    array_push($conflictingReservations, $reservationConflict);
                }

                if ($hasEquipment) {
                    $conflictingEquipmentReservations = $this->checkForEquipmentConflictsPendingReservation($equipmentIds, $activeReservation);
                    if (!empty($conflictingEquipmentReservations)) {
                        foreach ($conflictingEquipmentReservations as $conflictingEquipmentReservation){
                            array_push($conflictingReservations, $conflictingEquipmentReservation);
                        }
                    }
                }
            }
        }

        return $conflictingReservations;
    }

    /**
     * Find conflicting active reservations based on startTimeDate and endTimeDate of the current reservation.
     *
     * @param Reservation $currentReservation that is being checked
     * @param Reservation[] $activeReservations in the system
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    private function checkForTimeConflicts($currentReservation, $activeReservations)
    {
        if (!isset($currentReservation) || !isset($activeReservations)) {
            return [];
        }

        $hasEquipment = false;
        $equipments = $this->_equipmentManager->findEquipmentForReservation($currentReservation->getReservationID());
        if (!empty($equipments)) {
            $hasEquipment = true;
        }

        $conflictingReservations = [];
        foreach ($activeReservations as $activeReservation) {

            // Is the start of the current reservation contained between the start and end time of an active one?
            if ($currentReservation->getStartTimeDate() >= $activeReservation->getStartTimeDate()
                && $currentReservation->getStartTimeDate() <= $activeReservation->getEndTimeDate()
            ) {
                if ($currentReservation->getRoomId() == $activeReservation->getRoomId()) {
                    $reservationConflict = new ReservationConflict($activeReservation, "Overlapping start time.");
                    array_push($conflictingReservations, $reservationConflict);
                }

                if ($hasEquipment) {
                    $conflictingEquipmentReservations = $this->checkForEquipmentConflicts($equipments, $activeReservation);
                    if (!empty($conflictingEquipmentReservations)) {
                        foreach ($conflictingEquipmentReservations as $conflictingEquipmentReservation){
                            array_push($conflictingReservations, $conflictingEquipmentReservation);
                        }
                    }
                }
            } // Is the end of the current reservation contained between the start and end time of an active one?
            else if ($currentReservation->getEndTimeDate() >= $activeReservation->getStartTimeDate()
                && $currentReservation->getEndTimeDate() <= $activeReservation->getEndTimeDate()
            ) {
                if ($currentReservation->getRoomId() == $activeReservation->getRoomId()) {
                    $reservationConflict = new ReservationConflict($activeReservation, "Overlapping end time.");
                    array_push($conflictingReservations, $reservationConflict);
                }

                if ($hasEquipment) {
                    $conflictingEquipmentReservations = $this->checkForEquipmentConflicts($equipments, $activeReservation);
                    if (!empty($conflictingEquipmentReservations)) {
                        foreach ($conflictingEquipmentReservations as $conflictingEquipmentReservation){
                            array_push($conflictingReservations, $conflictingEquipmentReservation);
                        }
                    }
                }
            } // Does the current reservation contain the start and end of an active one?
            else if ($currentReservation->getStartTimeDate() <= $activeReservation->getStartTimeDate()
                && $currentReservation->getEndTimeDate() >= $activeReservation->getEndTimeDate()
            ) {
                if ($currentReservation->getRoomId() == $activeReservation->getRoomId()) {
                    $reservationConflict = new ReservationConflict($activeReservation, "Overlapping reservation times.");
                    array_push($conflictingReservations, $reservationConflict);
                }

                if ($hasEquipment) {
                    $conflictingEquipmentReservations = $this->checkForEquipmentConflicts($equipments, $activeReservation);
                    if (!empty($conflictingEquipmentReservations)) {
                        foreach ($conflictingEquipmentReservations as $conflictingEquipmentReservation){
                            array_push($conflictingReservations, $conflictingEquipmentReservation);
                        }
                    }
                }
            }
        }

        return $conflictingReservations;
    }

    /**
     * Find conflicting equipment requests for a pending reservation.
     *
     * @param array $equipmentIds that are being requested
     * @param Reservation $activeReservation in the system
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    private function checkForEquipmentConflictsPendingReservation($equipmentIds, $activeReservation)
    {
        if (empty($equipmentIds) || !isset($activeReservation)) {
            return [];
        }

        $equipmentsForActiveReservation = $this->_equipmentManager->findEquipmentForReservation($activeReservation->getReservationID());
        if (empty($equipmentsForActiveReservation)) {
            return [];
        }

        $conflictingEquipmentReservations = [];
        foreach ($equipmentIds as $equipmentId) {
            foreach ($equipmentsForActiveReservation as $equipmentForActiveReservation) {
                if ($equipmentId == $equipmentForActiveReservation->getEquipmentId()) {
                    $conflictingEquipmentReservation = new ReservationConflict($activeReservation, "Conflict with equipment Id: "
                        . $equipmentId);
                    array_push($conflictingEquipmentReservations, $conflictingEquipmentReservation);
                }
            }
        }

        return $conflictingEquipmentReservations;
    }

    /**
     * Find conflicting equipment loans.
     *
     * @param Equipment[] $equipmentsForCurrentReservation that is being checked
     * @param Reservation $activeReservation in the system
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    private function checkForEquipmentConflicts($equipmentsForCurrentReservation, $activeReservation)
    {
        if (!isset($currentReservation) || !isset($activeReservation)) {
            return [];
        }

        $equipmentsForActiveReservation = $this->_equipmentManager->findEquipmentForReservation($activeReservation->getReservationID());
        if (empty($equipmentsForActiveReservation)) {
            return [];
        }

        $conflictingEquipmentReservations = [];
        foreach ($equipmentsForCurrentReservation as $equipmentForCurrentReservation) {
            foreach ($equipmentsForActiveReservation as $equipmentForActiveReservation) {
                if ($equipmentForCurrentReservation->getEquipmentId() == $equipmentForActiveReservation->getEquipmentId()) {
                    $conflictingEquipmentReservation = new ReservationConflict($activeReservation, "Conflict with equipment Id: "
                        . $equipmentForCurrentReservation->getEquipmentId());
                    array_push($conflictingEquipmentReservations, $conflictingEquipmentReservation);
                    break;
                }
            }
        }

        return $conflictingEquipmentReservations;
    }
}