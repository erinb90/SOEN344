<?php

namespace Stark;

use Stark\Mappers\LoanContractMapper;
use Stark\Mappers\LoanedEquipmentMapper;
use Stark\Models\EquipmentRequest;
use Stark\Models\LoanContract;
use Stark\Models\LoanedEquipment;
use Stark\Models\Reservation;
use Stark\Mappers\ReservationMapper;
use Stark\RequestModels\ReservationRequest;
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
     * ModifyReservationSession constructor.
     */
    public function __construct()
    {
        $this->_ReservationMapper = new ReservationMapper();
        $this->_LoanedEquipmentMapper = new LoanedEquipmentMapper();
        $this->_ReservationManager = new ReservationManager();
        $this->_LoanContractMapper = new LoanContractMapper();
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

        // Check for conflicts
        $reservationConflicts = $this->_ReservationManager
            ->checkForConflicts($reservationId, $roomId, $newStartTimeDate, $newEndTimeDate, $newEquipmentRequests);

        // Get errors
        $errors = $this->_ReservationManager->convertConflictsToErrors($reservationConflicts);
        $equipmentReassignmentErrors = $this->_ReservationManager->assignAlternateEquipmentId($reservationConflicts, $equipmentRequests);

        // Merge errors to display to user
        $displayErrors = $this->mergeErrors($errors, $equipmentReassignmentErrors);

        $canBeAccommodated = true;

        // There were conflicts and re-assignment also caused errors
        if (!empty($reservationConflicts) && !empty($equipmentReassignmentErrors)) {
            $canBeAccommodated = false;
        }

        if ($canBeAccommodated) {
            if($changedEquipment){
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
            return [];
        } else {
            return $displayErrors;
        }
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