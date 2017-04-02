<?php

namespace Stark;

use Stark\Mappers\LoanContractMapper;
use Stark\Mappers\LoanedEquipmentMapper;
use Stark\RequestModels\EquipmentRequest;
use Stark\Models\LoanContract;
use Stark\Models\LoanedEquipment;
use Stark\Models\Reservation;
use Stark\Mappers\ReservationMapper;
use Stark\RequestModels\ReservationRequest;
use Stark\RequestModels\ReservationRequestBuilder;
use Stark\Utilities\ReservationManager;

class ModifyReservationSession
{
    /**
     * @var LoanedEquipmentMapper
     */
    private $_LoanedEquipmentMapper;

    /**
     * @var ReservationMapper
     */
    private $_ReservationMapper;

    /**
     * @var ReservationMapper
     */
    private $_ReservationManager;

    /**
     * @var LoanContractMapper
     */
    private $_LoanContractMapper;

    /**
     * @var String[]
     */
    private $_errors;

    /**
     * ModifyReservationSession constructor.
     */
    public function __construct()
    {
        $this->_ReservationMapper = new ReservationMapper();
        $this->_LoanedEquipmentMapper = new LoanedEquipmentMapper();
        $this->_ReservationManager = new ReservationManager();
        $this->_LoanContractMapper = new LoanContractMapper();
        $this->_errors = [];
    }

    /**
     * Returns the internal reservation mapper.
     *
     * @return \Stark\Mappers\ReservationMapper
     */
    public function getReservationMapper()
    {
        return $this->_ReservationMapper;
    }

    /**
     * Attempt to modify an existing reservation.
     *
     * @param int $reservationId of the reservation to modify.
     * @param boolean $changedEquipment if the user made an equipment change.
     * @param ReservationRequest $reservationRequest for the modification.
     * @return String[] errors to to display if the modification failed, or empty if succeeded.
     */
    public function modify($reservationId, $changedEquipment, $reservationRequest)
    {
        $equipmentRequests = $reservationRequest->getEquipmentRequests();
        $roomId = $reservationRequest->getRoomId();
        $newStartTimeDate = $reservationRequest->getStartTimeDate();
        $newEndTimeDate = $reservationRequest->getEndTimeDate();
        $newTitle = $reservationRequest->getTitle();

        // Current reservation
        /**
         * @var Reservation $reservation
         */
        $reservation = $this->_ReservationMapper->findByPk($reservationId);

        // Loaned equipment for the reservation
        $loanedEquipments = $this->getExistingLoanedEquipment($reservationId);

        // Filter new equipment requests
        $newEquipmentRequests = $this->filterNewEquipmentRequests($loanedEquipments, $equipmentRequests);

        // Filter equipment to remove, if the user made a modification to equipment
        $removedLoanedEquipment = [];
        if ($changedEquipment) {
            $removedLoanedEquipment = $this->filterRemovedLoanedEquipment($loanedEquipments, $equipmentRequests);
        }

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->roomId($roomId)
            ->startTimeDate($newStartTimeDate)
            ->endTimeDate($newEndTimeDate)
            ->equipmentRequests($newEquipmentRequests);
        $reservationRequest = $reservationRequestBuilder->build();

        $canBeAccommodated = $this->canBeAccommodated($reservationId, $reservationRequest);

        if ($canBeAccommodated) {
            if ($changedEquipment) {
                $this->addNewEquipment($reservationId, $newEquipmentRequests);
                $this->removeLoanedEquipment($removedLoanedEquipment);
            }

            $reservation->setStartTimeDate($newStartTimeDate);
            $reservation->setEndTimeDate($newEndTimeDate);
            $reservation->setTitle($newTitle);
            $reservation->setRoomId($roomId);
            $this->_ReservationMapper->uowUpdate($reservation);
            $this->_ReservationMapper->commit();
            $this->_ReservationManager->accommodateReservations();
        }
        return $this->_errors;
    }

    /**
     * @param $reservationId
     * @param $reservationRequest
     * @return bool specifying if the new reservation can be accommodated
     *
     * Checks for time and equipment conflicts, returns true if new res can be accommodated
     */
    public function canBeAccommodated($reservationId, $reservationRequest)
    {
        // Check for conflicts
        $reservationConflicts = $this->_ReservationManager
            ->checkForConflicts($reservationId, $reservationRequest);

        $canBeAccommodated = true;

        $errors = $this->_ReservationManager->convertConflictsToErrors($reservationConflicts);

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

        $equipmentReassignmentErrors = [];

        // There were time conflicts
        if ($hasTimeConflicts) {
            $canBeAccommodated = false;
        } else if ($hasEquipmentConflicts) {
            $equipmentReassignmentErrors = $this->_ReservationManager->assignAlternateEquipmentId($reservationConflicts, $newEquipmentRequests);

            // There were unresolved equipment conflicts
            if (!empty($equipmentReassignmentErrors)) {
                $canBeAccommodated = false;
            }
        }

        // Merge errors to display to user
        $this->_errors = $this->mergeErrors($errors, $equipmentReassignmentErrors);

        return $canBeAccommodated;
    }

    /**
     * Gets existing loaned equipment for the user.
     *
     * @param int $reservationId for the current reservation.
     * @return LoanedEquipment[] for the current reservation.
     */
    private function getExistingLoanedEquipment($reservationId)
    {
        /**
         * @var Reservation $reservation
         */
        return $this->_ReservationManager->getLoanedEquipmentForReservation($reservationId);
    }

    /**
     * Merges errors.
     *
     * @param string[] $errors
     * @param string[] $equipmentReassignmentErrors
     * @return string[] merged errors.
     */
    private function mergeErrors($errors, $equipmentReassignmentErrors)
    {
        /**
         * @var $string [] mergedErrors
         */
        $mergedErrors = [];
        foreach ($errors as $error) {
            $mergedErrors[] = $error;
        }

        foreach ($equipmentReassignmentErrors as $error) {
            $mergedErrors[] = $error;
        }

        return $mergedErrors;
    }

    /**
     * Perform remove of currently loaned equipment.
     *
     * @param LoanedEquipment[] $removedLoanedEquipment to remove.
     */
    private function removeLoanedEquipment($removedLoanedEquipment)
    {
        if (empty($removedLoanedEquipment)) {
            return;
        }

        foreach ($removedLoanedEquipment as $loanedEquipment) {
            $this->_LoanedEquipmentMapper->uowDelete($loanedEquipment);
        }

        $this->_LoanedEquipmentMapper->commit();
    }

    /**
     * Perform addition of new equipment.
     *
     * @param int $reservationId of the current reservation.
     * @param EquipmentRequest[] $newEquipmentRequests for the reservation.
     */
    private function addNewEquipment($reservationId, $newEquipmentRequests)
    {
        if (empty($newEquipmentRequests)) {
            return;
        }

        // Search for existing loan contract
        $loanContract = $this->_LoanContractMapper->findByReservationId($reservationId);
        if ($loanContract == null) {
            // Create new loan contract if none
            $loanContract = new LoanContract();
            $loanContract->setReservationId($reservationId);
            $this->_LoanContractMapper->uowInsert($loanContract);
            $this->_LoanContractMapper->commit();
        }

        foreach ($newEquipmentRequests as $i => $newEquipmentRequest) {
            $loanedEquipmentEntry = new LoanedEquipment();
            $loanedEquipmentEntry->setEquipmentId($newEquipmentRequest->getEquipmentId());
            $loanedEquipmentEntry->setLoanContractId($loanContract->getLoanContractiD());
            $this->_LoanedEquipmentMapper->uowInsert($loanedEquipmentEntry);
        }

        $this->_LoanedEquipmentMapper->commit();
    }

    /**
     * Resolves new equipment requests.
     *
     * @param LoanedEquipment[] $loanedEquipments of the existing reservation.
     * @param EquipmentRequest[] $equipmentRequests for the reservation including existing.
     * @return EquipmentRequest[] that will be added to the reservation.
     */
    private function filterNewEquipmentRequests($loanedEquipments, $equipmentRequests)
    {
        /**
         * @var EquipmentRequest[] $newEquipmentRequests
         */
        $newEquipmentRequests = [];

        $takenEquipmentIds = [];
        foreach ($loanedEquipments as $loanedEquipment) {
            $takenEquipmentIds[] = $loanedEquipment->getEquipmentId();
        }
        foreach ($equipmentRequests as $equipmentRequest) {
            $isFound = in_array($equipmentRequest->getEquipmentId(), $takenEquipmentIds);
            if (!$isFound) {
                $newEquipmentRequests[] = $equipmentRequest;
            }
        }
        return $newEquipmentRequests;
    }

    /**
     * Resolves removed equipment requests.
     *
     * @param LoanedEquipment[] $loanedEquipments of the existing reservation.
     * @param EquipmentRequest[] $equipmentRequests for the reservation including existing.
     * @return LoanedEquipment[] that will be removed from the reservation.
     */
    private function filterRemovedLoanedEquipment($loanedEquipments, $equipmentRequests)
    {
        /**
         * @var LoanedEquipment[] $removedLoanedEquipment
         */
        $removedLoanedEquipment = [];

        $chosenEquipmentIds = [];
        foreach ($equipmentRequests as $equipmentRequest) {
            $chosenEquipmentIds[] = $equipmentRequest->getEquipmentId();
        }
        foreach ($loanedEquipments as $loanedEquipment) {
            $isFound = in_array($loanedEquipment->getEquipmentId(), $chosenEquipmentIds);
            if (!$isFound) {
                $removedLoanedEquipment[] = $loanedEquipment;
            }
        }
        return $removedLoanedEquipment;
    }
}