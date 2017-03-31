<?php

namespace Stark;

use Stark\Mappers\LoanContractMapper;
use Stark\Mappers\LoanedEquipmentMapper;
use Stark\Models\EquipmentRequest;
use Stark\Models\LoanContract;
use Stark\Models\LoanedEquipment;
use Stark\Models\Reservation;
use Stark\Mappers\ReservationMapper;
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
     * @param int $roomId of the reservation to modify
     * @param String $newStartTimeDate of the reservation to modify.
     * @param String $newEndTimeDate of the reservation to modify.
     * @param String $newTitle of the reservation to modify.
     * @param boolean $changedEquipment if the user made an equipment change.
     * @param boolean $computerAlt if the user allowed alternative computers.
     * @param boolean $projectorAlt if the user allowed alternative projectors.
     * @param EquipmentRequest[] $equipmentRequests for the modification.
     * @return String[] errors to to display if the modification failed, or empty if succeeded.
     */
    public function modify($reservationId, $roomId, $newStartTimeDate, $newEndTimeDate, $newTitle, $changedEquipment, $computerAlt, $projectorAlt, $equipmentRequests)
    {
        /**
         * @var Reservation $reservation
         */
        $reservation = $this->_ReservationMapper->findByPk($reservationId);
        $loanedEquipments = $this->_ReservationManager->getLoanedEquipmentForReservation($reservationId);

        $newEquipmentRequests = $this->filterNewEquipmentRequests($loanedEquipments, $equipmentRequests);
        $removedLoanedEquipment = [];
        if ($changedEquipment) {
            $removedLoanedEquipment = $this->filterRemovedLoanedEquipment($loanedEquipments, $equipmentRequests);
        }

        $reservationId = $reservation->getReservationID();
        $reservationConflicts = $this->_ReservationManager
            ->checkForConflicts($reservationId, $roomId, $newStartTimeDate, $newEndTimeDate, $newEquipmentRequests);

        /**
         * @var String[] $displayErrors
         */
        $errors = [];
        $canBeAccommodated = true;

        if (!empty($reservationConflicts)) {

            $errors = $this->_ReservationManager->assignAlternateEquipmentId($reservationConflicts, $equipmentRequests, $computerAlt, $projectorAlt);

            if (!empty($errors)) {
                $canBeAccommodated = false;
            }
        }

        if ($canBeAccommodated) {
            $loanContract = $this->_LoanContractMapper->findByReservationId($reservationId);
            if ($loanContract == null) {
                $loanContract = new LoanContract();
                $loanContract->setReservationId($reservationId);
                $this->_LoanContractMapper->uowInsert($loanContract);
                $this->_LoanContractMapper->commit();
            }

            $loanContract = $this->_LoanContractMapper->findByReservationId($reservationId);

            // Schedule remove of currently loaned equipment
            foreach ($removedLoanedEquipment as $loanedEquipment) {
                $this->_LoanedEquipmentMapper->uowDelete($loanedEquipment);
            }
            if ($changedEquipment) {
                $this->_LoanedEquipmentMapper->commit();
            }

            // Schedule addition of newly loaned equipment
            foreach ($newEquipmentRequests as $i => $newEquipmentRequest) {
                $loanedEquipmentEntry = new LoanedEquipment();
                $loanedEquipmentEntry->setEquipmentId($newEquipmentRequest->getEquipmentId());
                $loanedEquipmentEntry->setLoanContractId($loanContract->getLoanContractiD());
                $this->_LoanedEquipmentMapper->uowInsert($loanedEquipmentEntry);
            }
            if ($changedEquipment) {
                $this->_LoanedEquipmentMapper->commit();
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
            return $errors;
        }
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