<?php

namespace Stark;

use Stark\Interfaces\Equipment;
use Stark\Mappers\LoanedEquipmentMapper;
use Stark\Models\EquipmentRequest;
use Stark\Models\Reservation;
use Stark\Mappers\ReservationMapper;
use Stark\Utilities\ReservationManager;

class ModifyReservationSession
{
    /**
     * @var int
     */
    private $_reservationId;

    /**
     * @var String
     */
    private $_newStartTime;

    /**
     * @var  String
     */
    private $_newEndTime;

    /**
     * @var int
     */
    private $_newRoomId;

    /**
     * @var String
     */
    private $_newTitle;

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
     * @param $reservationId
     */
    private function __construct($reservationId)
    {
        $this->_reservationId = $reservationId;
        $this->_ReservationMapper = new ReservationMapper();
        $this->_LoanedEquipmentMapper = new LoanedEquipmentMapper();
        $this->_ReservationManager = new ReservationManager();
    }

    public function getReservation()
    {
        /**
         * @var $Reservation Reservation
         */
        $Reservation = $this->_ReservationMapper->findByPk($this->_reservationId);

        return $Reservation;
    }

    /**
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
     * @param String $newStartTime of the reservation to modify.
     * @param String $newEndTime of the reservation to modify.
     * @param String $newTitle of the reservation to modify.
     * @return String[] errors to to display if the modification failed, or empty if succeeded.
     */
    public function modify($reservationId, $newStartTime, $newEndTime, $newTitle)
    {
        /**
         * @var Reservation $reservation
         */
        $reservation = $this->_ReservationMapper->findByPk($reservationId);
        $loanedEquipments = $this->_ReservationManager->getLoanedEquipmentForReservation($reservationId);
        /**
         * @var EquipmentRequest[] $equipmentRequests
         */
        $equipmentRequests = [];
        foreach ($loanedEquipments as $loanedEquipment) {
            /**
             * @var Equipment $equipment
             */
            $equipment = $this->_ReservationManager->getEquipmentForId($loanedEquipment->getEquipmentId());
            $equipmentRequests[] = new EquipmentRequest($equipment->getEquipmentId(), $equipment->getDiscriminator());
        }

        $roomId = $reservation->getRoomId();
        $reservationConflicts = $this->_ReservationManager
            ->checkForConflicts($roomId, $newStartTime, $newEndTime, $equipmentRequests);

        /**
         * @var String[] $displayErrors
         */
        $displayErrors = [];
        $canBeAccommodated = true;

        if (!isEmpty($reservationConflicts)) {

            // Log time conflicts
            foreach ($reservationConflicts as $reservationConflict) {
                foreach ($reservationConflict->getDateTimes() as $timeConflict) {
                    $displayErrors[] = "Conflict with time: " . $timeConflict;
                }
            }

            $errors = $this->_ReservationManager->assignAlternateEquipmentId($reservationConflicts, $equipmentRequests);

            if (!isEmpty($errors)) {
                foreach ($errors as $error) {
                    $displayErrors[] = $error;
                }
                $canBeAccommodated = false;
            } else {
                // Re-map loaned equipment with new ids
                foreach ($equipmentRequests as $i => $equipmentRequest) {
                    $loanedEquipments[$i]->setEquipmentId($equipmentRequest->getEquipmentId());
                    $this->_LoanedEquipmentMapper->uowUpdate($loanedEquipments[$i]);
                }
            }
        }

        if ($canBeAccommodated) {
            $reservation->setStartTimeDate($newStartTime);
            $reservation->setEndTimeDate($newEndTime);
            $reservation->setTitle($newTitle);
            $this->_ReservationMapper->uowUpdate($reservation);
            $this->_ReservationMapper->commit();
            return [];
        } else {
            return $displayErrors;
        }
    }
}