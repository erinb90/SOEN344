<?php

namespace Stark\Utilities;

use Stark\Enums\EquipmentType;
use Stark\Interfaces\Equipment;
use Stark\Mappers\LoanedEquipmentMapper;
use Stark\Mappers\ReservationMapper;
use Stark\Mappers\UserMapper;
use Stark\RequestModels\EquipmentRequest;
use Stark\Models\LoanedEquipment;
use Stark\Models\Reservation;
use Stark\Models\User;
use Stark\RequestModels\ReservationRequest;
use Stark\RequestModels\ReservationRequestBuilder;

class ReservationManager
{
    /**
     * @var \Stark\Mappers\ReservationMapper $_reservationMapper to retrieve reservations
     */
    private $_reservationMapper;

    /**
     * @var \Stark\Mappers\LoanedEquipmentMapper $_loanedEquipmentMapper to retrieve loaned equipment
     */
    private $_loanedEquipmentMapper;

    /**
     * @var \Stark\Utilities\EquipmentManager $_equipmentManager to manage equipment
     */
    private $_equipmentManager;

    /**
     * @var \Stark\Mappers\UserMapper $_userMapper to map users
     */
    private $_userMapper;

    /**
     * ReservationManager constructor.
     */
    public function __construct()
    {
        $this->_reservationMapper = new ReservationMapper();
        $this->_loanedEquipmentMapper = new LoanedEquipmentMapper();
        $this->_equipmentManager = new EquipmentManager();
        $this->_userMapper = new UserMapper();
    }

    /**
     * Queries the position of the reservation in the wait list.
     *
     * @param int $reservationId of the reservation to query in the wait list.
     * @return int the reservation position.
     */
    public function getWaitListPosition($reservationId)
    {
        $reservations = $this->getOrderedWaitingReservations();
        foreach ($reservations as $i => $reservation) {
            if ($reservation->getReservationID() == $reservationId) {
                return $i + 1;
            }
        }
        return -1;
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
     * Attempts to accommodate wait listed reservations after a change to an active one.
     */
    public function accommodateWaitlistedReservations()
    {
        $waitList = $this->getOrderedWaitingReservations();

        do {
            // do-while end condition to indicate when no additional wait listed reservations
            // can be changed to confirmed
            $reservationWasAccommodated = false;

            // Go through the wait list
            foreach ($waitList as $waitingReservation) {
                // If the current reservation was accommodated
                $canBeAccommodated = true;

                $equipmentRequests = [];
                $loanedEquipments = $this->getLoanedEquipmentForReservation($waitingReservation->getReservationID());
                if (isset($loanedEquipments) && !empty($loanedEquipments)) {
                    /**
                     * @var EquipmentRequest[] $equipmentRequests
                     */
                    foreach ($loanedEquipments as $loanedEquipment) {
                        $equipment = $this->getEquipmentForId($loanedEquipment->getEquipmentId());
                        if ($equipment != null) {
                            $equipmentRequests[] = new EquipmentRequest($equipment->getEquipmentId(), $equipment->getDiscriminator(), true);
                        }
                    }
                }

                $reservationRequestBuilder = new ReservationRequestBuilder();
                $reservationRequestBuilder
                    ->roomId($waitingReservation->getRoomId())
                    ->startTimeDate($waitingReservation->getStartTimeDate())
                    ->endTimeDate($waitingReservation->getEndTimeDate())
                    ->equipmentRequests($equipmentRequests);
                $reservationRequest = $reservationRequestBuilder->build();

                $equipmentRequests = $reservationRequest->getEquipmentRequests();
                $reservationConflicts = $this->checkForConflicts($waitingReservation->getReservationID(), $reservationRequest);

                $hasTimeConflicts = false;
                $hasEquipmentConflicts = true;
                foreach ($reservationConflicts as $reservationConflict) {
                    if (!empty($reservationConflict->getDateTimes())) {
                        $hasTimeConflicts = true;
                    }
                    if (!empty($reservationConflict->getEquipments())) {
                        $hasEquipmentConflicts = true;
                    }
                }

                // There were time conflicts or re-assignment also caused errors
                if ($hasTimeConflicts || !empty($equipmentReassignmentErrors)) {
                    $canBeAccommodated = false;
                } else if($hasEquipmentConflicts) {
                    $equipmentReassignmentErrors = $this->assignAlternateEquipmentId($reservationConflicts, $equipmentRequests);
                    if (empty($equipmentReassignmentErrors)) {
                        // Re-map loaned equipment with new ids
                        foreach ($equipmentRequests as $i => $equipmentRequest) {
                            $loanedEquipments[$i]->setEquipmentId($equipmentRequest->getEquipmentId());
                            $this->_loanedEquipmentMapper->uowUpdate($loanedEquipments[$i]);
                        }
                        $this->_loanedEquipmentMapper->commit();
                    }
                }

                // Update the reservation status to active
                if ($canBeAccommodated) {
                    $reservationWasAccommodated = true;
                    $waitingReservation->setIsWaited(false);
                    $this->_reservationMapper->uowUpdate($waitingReservation);
                    $this->_reservationMapper->commit();
                    break;
                }
            }

            // Refresh the list
            $waitList = $this->getOrderedWaitingReservations();
        } while ($reservationWasAccommodated);
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
     * @param int $reservationId of the the reservation
     * @param ReservationRequest $reservationRequest to check for conflicts.
     *
     * @return ReservationConflict[] with active reservations.
     */
    public function checkForConflicts($reservationId, $reservationRequest)
    {
        $activeReservations = $this->_reservationMapper->findAllActive();
        return $this->checkForTimeConflicts($reservationId, $reservationRequest, $activeReservations);
    }

    /**
     * Converts reservation conflicts into errors.
     *
     * @param ReservationConflict[] $reservationConflicts when creating a reservation.
     * @return string[] of errors to log to the user.
     */
    public function convertConflictsToErrors($reservationConflicts)
    {
        /**
         * @var string[] $errors to log to the user.
         */
        $errors = [];
        foreach ($reservationConflicts as $reservationConflict) {
            // Log time conflicts
            foreach ($reservationConflict->getDateTimes() as $timeConflict) {
                $errors[] = "Conflict with time: " . $timeConflict;
            }
            // Log equipment conflicts
            foreach ($reservationConflict->getEquipments() as $equipmentConflict) {
                $errors[] = "Conflict with equipment ID " . $equipmentConflict->getEquipmentId();
            }
        }
        return $errors;
    }

    /**
     * Attempts to assign new equipment ids for any equipment request conflicts.
     *
     * @param ReservationConflict[] $reservationConflicts from the attempted reservation booking.
     * @param EquipmentRequest[] $equipmentRequests for the reservation.
     *
     * @return string[] of errors from equipment re-assignment.
     */
    public function assignAlternateEquipmentId($reservationConflicts, &$equipmentRequests)
    {
        // No conflicts, so return
        if (empty($reservationConflicts)) {
            return [];
        }

        // Extract conflicting ids
        $conflictingIds = [];
        foreach ($reservationConflicts as $reservationConflict) {
            foreach ($reservationConflict->getEquipments() as $equipmentConflict) {
                $conflictingIds[] = $equipmentConflict->getEquipmentId();
            }
        }

        /**
         * @var EquipmentRequest[] $nonAssignedEquipment
         */
        $nonAssignedEquipment = [];
        $assignedIds = [];

        // Filter equipment requests that need to be re-assigned
        foreach ($equipmentRequests as $equipmentRequest) {
            $needReassignment = in_array($equipmentRequest->getEquipmentId(), $conflictingIds);
            if ($needReassignment) {
                $nonAssignedEquipment[] = $equipmentRequest;
            } else {
                $assignedIds[] = $equipmentRequest->getEquipmentId();
            }
        }

        /**
         * @var string[] $errors from equipment re-assignment.
         */
        $errors = [];

        /* Attempt to resolve equipment conflicts.
        This algorithm checks for available equipment ids for the same equipment type,
        and re-assigns the id for the equipment request to one that is available.
        Once re-assigned, that available equipment id is considered assigned, and cannot be given to another
        request that needs an alternative.
        */
        foreach ($nonAssignedEquipment as $equipmentRequest) {
            $wasAssigned = false;
            $allowAlternative = $equipmentRequest->allowAssignAlternative();
            if ($allowAlternative) {
                $availableEquipmentIds = $this->findAvailableEquipmentIds($equipmentRequest->getEquipmentType());
                foreach ($availableEquipmentIds as $availableEquipmentId) {
                    $isAlreadyAssigned = in_array($availableEquipmentId, $assignedIds);
                    if (!$isAlreadyAssigned) {
                        $equipmentRequest->setEquipmentId($availableEquipmentId);
                        $assignedIds[] = $availableEquipmentId;
                        $wasAssigned = true;
                        break;
                    }
                }
            }

            if (!$wasAssigned) {
                $this->noAlternativeError($equipmentRequest, $errors, $allowAlternative);
            }
        }

        return $errors;
    }

    /**
     * Add an error that no alternative equipment could be found.
     *
     * @param EquipmentRequest $equipmentRequest from the attempted reservation booking.
     * @param String[] $errors to add.
     * @param boolean $allowAlternative for the equipment.
     *
     * @return void
     */
    private function noAlternativeError(&$equipmentRequest, &$errors, $allowAlternative)
    {
        $equipment = $this->_equipmentManager->getEquipmentForId($equipmentRequest->getEquipmentId());

        $equipmentType = 'Unknown';
        // This is awful, but would require a refactor in the database
        if ($equipmentRequest->getEquipmentType() == EquipmentType::Computer) {
            $equipmentType = 'Computer';
        } else if ($equipmentRequest->getEquipmentType() == EquipmentType::Projector) {
            $equipmentType = 'Projector';
        }
        if ($allowAlternative) {
            $errors[] = "No alternative " . $equipmentType . " could be found for "
                . $equipment->getManufacturer() . " - " . $equipment->getProductLine();
        } else {
            $errors[] = "No alternative allowed for "
                . $equipment->getManufacturer() . " - " . $equipment->getProductLine();
        }
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
     * @param int $reservationId of the reservation.
     * @param ReservationRequest $reservationRequest to check for conflicts.
     * @param Reservation[] $activeReservations in the system
     *
     * @return ReservationConflict[] of conflicting reservations or empty if none
     */
    private function checkForTimeConflicts($reservationId, $reservationRequest, $activeReservations)
    {
        if (!isset($reservationId) || !isset($reservationRequest) || empty($activeReservations)) {
            return [];
        }

        $roomId = $reservationRequest->getRoomId();
        $equipmentRequests = $reservationRequest->getEquipmentRequests();
        $startTimeDate = $reservationRequest->getStartTimeDate();
        $endTimeDate = $reservationRequest->getEndTimeDate();

        $hasEquipment = !empty($equipmentRequests);
        $conflictingReservations = [];

        foreach ($activeReservations as $activeReservation) {
            if ($reservationId == $activeReservation->getReservationID()) {
                // Same reservation
                continue;
            }

            $reservationConflict = new ReservationConflict($activeReservation);

            // Does the current reservation conflict with the start time of an active reservation?
            $conflictingStart = $startTimeDate <= $activeReservation->getStartTimeDate()
                && $endTimeDate >= $activeReservation->getStartTimeDate()
                && $endTimeDate <= $activeReservation->getEndTimeDate();

            // Does the current reservation conflict with the end time of an active reservation?
            $conflictingEnd = $startTimeDate >= $activeReservation->getStartTimeDate()
                && $startTimeDate <= $activeReservation->getEndTimeDate()
                && $endTimeDate >= $activeReservation->getEndTimeDate();

            // Does the current reservation contain the start and end of an active one?
            $containsActive = $startTimeDate <= $activeReservation->getStartTimeDate()
                && $endTimeDate >= $activeReservation->getEndTimeDate();

            // Does the current reservation contain the start and end of an active one?
            $isContainedInActive = $startTimeDate > $activeReservation->getStartTimeDate()
                && $endTimeDate < $activeReservation->getEndTimeDate();

            // Check for conflicting dates
            if ($roomId == $activeReservation->getRoomId() && !$activeReservation->isIsWaited()) {
                // Log error based on time that caused the conflict
                if ($containsActive || $isContainedInActive) {
                    $reservationConflict->addDateTime($activeReservation->getStartTimeDate());
                    $reservationConflict->addDateTime($activeReservation->getEndTimeDate());
                } else {
                    if ($conflictingStart) {
                        $reservationConflict->addDateTime($activeReservation->getStartTimeDate());
                    }

                    if ($conflictingEnd) {
                        $reservationConflict->addDateTime($activeReservation->getEndTimeDate());
                    }
                }
            }

            $hasTimeConflict = $containsActive || $conflictingStart || $conflictingEnd;
            if ($hasEquipment && $hasTimeConflict) {
                $this->checkForEquipmentConflicts($equipmentRequests, $activeReservation, $reservationConflict);
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

        $requestedEquipmentIds = [];
        foreach ($equipmentRequests as $equipmentRequest) {
            $requestedEquipmentIds[] = $equipmentRequest->getEquipmentId();
        }

        foreach ($equipmentsForActiveReservation as $equipmentForActiveReservation) {
            $isAlreadyAssigned = in_array($equipmentForActiveReservation->getEquipmentId(), $requestedEquipmentIds);
            if ($isAlreadyAssigned) {
                $reservationConflict->addEquipment($equipmentForActiveReservation);
            }
        }
    }
}