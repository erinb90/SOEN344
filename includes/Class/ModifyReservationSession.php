<?php

namespace Stark;

use Stark\Interfaces\Equipment;
use Stark\Mappers\LoanedEquipmentMapper;
use Stark\Models\EquipmentRequest;
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
     * ModifyReservationSession constructor.
     */
    public function __construct()
    {
        $this->_ReservationMapper = new ReservationMapper();
        $this->_LoanedEquipmentMapper = new LoanedEquipmentMapper();
        $this->_ReservationManager = new ReservationManager();
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
     * @param String $newDate of the reservation to modify.
     * @param String $newStartTimeDate of the reservation to modify.
     * @param String $newEndTimeDate of the reservation to modify.
     * @param String $newTitle of the reservation to modify.
     * @param EquipmentRequest[] $equipmentRequests for the modification.
     * @return String[] errors to to display if the modification failed, or empty if succeeded.
     */
    public function modify($reservationId, $newDate, $newStartTimeDate, $newEndTimeDate, $newTitle, $equipmentRequests)
    {
        /**
         * @var Reservation $reservation
         */
        $reservation = $this->_ReservationMapper->findByPk($reservationId);
        $loanedEquipments = $this->_ReservationManager->getLoanedEquipmentForReservation($reservationId);

        $newEquipmentRequests = $this->filterNewEquipmentRequests($loanedEquipments, $equipmentRequests);
        $removedLoanedEquipment = $this->filterRemovedLoanedEquipment($loanedEquipments, $equipmentRequests);

        // TODO : Remove equipments that the user no longer wants then check for conflicts with newly request equipment
        // Ignore unchanged equipment requests

        $roomId = $reservation->getRoomId();
        $reservationId = $reservation->getReservationID();
        $reservationConflicts = $this->_ReservationManager
            ->checkForConflicts($reservationId, $roomId, $newStartTimeDate, $newEndTimeDate, $newEquipmentRequests);

        /**
         * @var String[] $displayErrors
         */
        $errors = [];
        $canBeAccommodated = true;

        if (!empty($reservationConflicts)) {

            $errors = $this->_ReservationManager->assignAlternateEquipmentId($reservationConflicts, $equipmentRequests);

            if (!empty($errors)) {
                $canBeAccommodated = false;
            } else {
                // Re-map loaned equipment with new ids
                foreach ($equipmentRequests as $i => $equipmentRequest) {
                    $loanedEquipments[$i]->setEquipmentId($equipmentRequest->getEquipmentId());
                    $this->_LoanedEquipmentMapper->uowUpdate($loanedEquipments[$i]);
                }
                // Schedule remove of currently loaned equipment
                foreach ($removedLoanedEquipment as $loanedEquipment) {
                    $this->_LoanedEquipmentMapper->uowDelete($loanedEquipment);
                }
            }
        }

        if ($canBeAccommodated) {
            $reservation->setCreatedOn($newDate);
            $reservation->setStartTimeDate($newStartTimeDate);
            $reservation->setEndTimeDate($newEndTimeDate);
            $reservation->setTitle($newTitle);
            $this->_ReservationMapper->uowUpdate($reservation);
            $this->_LoanedEquipmentMapper->commit();
            $this->_ReservationMapper->commit();
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