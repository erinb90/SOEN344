<?php

namespace Stark\Utilities;

use Stark\Interfaces\Equipment;
use Stark\Mappers\ReservationMapper;
use Stark\Mappers\UserMapper;
use Stark\Models\EquipmentRequest;
use Stark\Models\LoanedEquipment;
use Stark\Models\Reservation;
use Stark\Models\User;

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
     * @var \Stark\Mappers\UserMapper $_userMapper to map users
     */
    private $_userMapper;

    /**
     * WaitlistManager constructor.
     */
    public function __construct()
    {
        $this->_reservationMapper = new ReservationMapper();
        $this->_equipmentManager = new EquipmentManager();
        $this->_userMapper = new UserMapper();
    }

    /**
     * Gets all wait list reservations sorted in order.
     *
     * @return Reservation[] of waitlisted reservations or empty if none
     */
    public function getOrderedWaitingReservations()
    {
        $reservations = $this->_reservationMapper->findAllWaitlisted();
        ksort($reservations);

        // Cache the students
        $capstoneStudentsReservation = [];
        $regularStudentsReservation = [];

        /**
         * @var Reservation $reservation
         */
        foreach ($reservations as $reservationId => $reservation) {
            $userId = $reservation->getUserId();
            /**
             * @var User $user
             */
            $user = $this->_userMapper->findByPk($userId);

            // Sort these users
            if ($user->isCapstoneStudent()) {
                $capstoneStudentsReservation[] = $reservation;
            } else {
                $regularStudentsReservation[] = $reservation;
            }

        }

        // If Capstone students were found, merge arrays then return
        if (!empty($capstoneStudentsReservation)) {
            return array_merge($capstoneStudentsReservation, $regularStudentsReservation);
        }

        return $regularStudentsReservation;
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
     * Gets active reservations.
     *
     * @return array of active reservations or empty if none
     */
    public function getAllActiveReservations()
    {
        return $this->_reservationMapper->findAllActive();
    }

    /**
     * Gets all equipment.
     *
     * @return Equipment[] of equipment or empty if none
     */
    public function getAllEquipment()
    {
        return $this->_equipmentManager->getAllEquipment();
    }

    /**
     * Gets available equipment ids based on active reservations.
     *
     * @param string $equipmentType of the requested equipment.
     *
     * @return int[] equipmentIds of an available equipment in the system or empty if none
     */
    public function findAvailableEquipmentIds($equipmentType)
    {
        /**
         * @var Reservation[] $activeReservations
         */
        $activeReservations = $this->_reservationMapper->findAllActive();
        $takenEquipmentIds = [];
        foreach ($activeReservations as $activeReservation) {
            $loanedEquipments = $this->getLoanedEquipmentForReservation($activeReservation->getReservationID());
            foreach ($loanedEquipments as $loanedEquipment) {
                $takenEquipmentIds[] = $loanedEquipment->getEquipmentId();
            }
        }

        $availableEquipmentIds = [];
        $equipments = $this->getAllEquipment();
        foreach ($equipments as $equipment) {
            if ($equipment->getDiscriminator() != $equipmentType) {
                continue;
            }

            $isFound = in_array($equipment->getEquipmentId(), $takenEquipmentIds);
            if (!$isFound) {
                $availableEquipmentIds[] = $equipment->getEquipmentId();
            }
        }

        return $availableEquipmentIds;
    }


    /**
     * Gets all loaned equipment for a reservation id.
     *
     * @param int $reservationId of the reservation
     *
     * @return LoanedEquipment[] of loaned equipment or empty if none
     */
    public function getLoanedEquipmentForReservation($reservationId)
    {
        return $this->_equipmentManager->findEquipmentForReservation($reservationId);
    }

    /**
     * Gets equipment based on id.
     *
     * @param int $equipmentId for the requested equipment.
     *
     * @return Equipment equipment in the system or null if not found
     */
    public function getEquipmentForId($equipmentId)
    {
        return $this->_equipmentManager->getEquipmentForId($equipmentId);
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
        $reservations = $this->_reservationMapper->findAllActive();
        return $this->checkForTimeConflicts($roomId, $startTimeDate, $endTimeDate, $reservations, $equipmentRequests);
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
                if ($roomId == $activeReservation->getRoomId() && !$activeReservation->isIsWaited()) {
                    $reservationConflict->addDateTime($activeReservation->getStartTimeDate());
                    $reservationConflict->addDateTime($activeReservation->getEndTimeDate());
                }

                if ($hasEquipment) {
                    $this->checkForEquipmentConflicts($equipmentRequests, $activeReservation, $reservationConflict);
                }
            } // Is the end of the current reservation contained between the start and end time of an active one?
            else if ($endTimeDate >= $activeReservation->getStartTimeDate()
                && $endTimeDate <= $activeReservation->getEndTimeDate()
            ) {
                if ($roomId == $activeReservation->getRoomId() && !$activeReservation->isIsWaited()) {
                    $reservationConflict->addDateTime($activeReservation->getStartTimeDate());
                    $reservationConflict->addDateTime($activeReservation->getEndTimeDate());
                }

                if ($hasEquipment) {
                    $this->checkForEquipmentConflicts($equipmentRequests, $activeReservation, $reservationConflict);
                }
            } // Does the current reservation contain the start and end of an active one?
            else if ($startTimeDate <= $activeReservation->getStartTimeDate()
                && $endTimeDate >= $activeReservation->getEndTimeDate()
            ) {
                if ($roomId == $activeReservation->getRoomId() && !$activeReservation->isIsWaited()) {
                    $reservationConflict->addDateTime($activeReservation->getStartTimeDate());
                    $reservationConflict->addDateTime($activeReservation->getEndTimeDate());
                }

                if ($hasEquipment) {
                    $this->checkForEquipmentConflicts($equipmentRequests, $activeReservation, $reservationConflict);
                }
            }

            if (!empty($reservationConflict->getDateTimes()) || !empty($reservationConflict->getEquipments())) {
                $conflictingReservations[] = $reservationConflict;
            }
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

        foreach ($equipmentsForActiveReservation as $equipmentForActiveReservation) {
            foreach ($equipmentRequests as $equipmentRequest) {
                if ($equipmentRequest->getEquipmentId() == $equipmentForActiveReservation->getEquipmentId()) {
                    $reservationConflict->addEquipment($equipmentForActiveReservation);
                }
            }
        }
    }
}