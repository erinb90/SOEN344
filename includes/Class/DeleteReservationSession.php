<?php
namespace Stark {

    use Stark\Enums\EquipmentType;
    use Stark\Mappers\LoanedEquipmentMapper;
    use Stark\Mappers\ReservationMapper;
    use Stark\Models\EquipmentRequest;
    use Stark\Models\Reservation;
    use Stark\Utilities\ReservationConflict;
    use Stark\Utilities\ReservationManager;


    /**
     * Class DeleteReservationSession
     * @package Stark
     */
    class DeleteReservationSession
    {
        private $_reservationId;

        /**
         * @var ReservationManager $_reservationManager to check for reservation conflicts.
         */
        private $_reservationManager;

        private $_ReservationMapper;

        /**
         * @var LoanedEquipmentMapper $_loanedEquipmentMapper to update loaned equipment.
         */
        private $_loanedEquipmentMapper;

        /***
         * DeleteReservationSession constructor.
         *
         * @param $reservationId
         */
        private function __construct($reservationId)
        {
            $this->_reservationId = $reservationId;
            $this->_reservationManager = new ReservationManager();
            $this->_ReservationMapper = new ReservationMapper();
            $this->_loanedEquipmentMapper = new LoanedEquipmentMapper();
        }

        /**
         * @return \Stark\Models\Reservation|null
         */
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
         * @return \Stark\Mappers\LoanedEquipmentMapper
         */
        public function getLoanedEquipmentMapper()
        {
            return $this->_loanedEquipmentMapper;
        }

        /**
         * @return \Stark\Utilities\ReservationManager
         */
        public function getReservationManager()
        {
            return $this->_reservationManager;
        }

        /**
         * @param $reservationId
         *
         * @return bool
         */
        public static function delete($reservationId)
        {
            $Session = new DeleteReservationSession($reservationId);

            $waitList = $Session->getReservationManager()->getOrderedWaitingReservations();

            $currentReservation = $Session->getReservation();

            $Session->getReservationMapper()->uowDelete($currentReservation);
            $Session->getReservationMapper()->commit();

            // No next reservation
            if (empty($waitList)) {
                return false;
            }

            do {
                $reservationWasAccommodated = false;
                foreach ($waitList as $waitingReservation) {
                    $canBeAccommodated = false;

                    $equipmentRequests = [];
                    $loanedEquipments = $Session->getReservationManager()->getLoanedEquipmentForReservation($waitingReservation->getReservationID());
                    if (isset($loanedEquipments) && !empty($loanedEquipments)) {
                        /**
                         * @var EquipmentRequest[] $equipmentRequests
                         */
                        foreach ($loanedEquipments as $loanedEquipment) {
                            $equipment = $Session->getReservationManager()->getEquipmentForId($loanedEquipment->getEquipmentId());
                            if ($equipment != null) {
                                $equipmentRequests[] = new EquipmentRequest($equipment->getEquipmentId(), $equipment->getDiscriminator());
                            }
                        }
                    }

                    $reservationConflicts = $Session->getReservationManager()
                        ->checkForConflicts($waitingReservation->getRoomId(), $waitingReservation->getStartTimeDate(), $waitingReservation->getEndTimeDate(), $equipmentRequests);

                    // If required
                    $errors = $Session->assignAlternateEquipmentId($reservationConflicts, $equipmentRequests);

                    if (empty($reservationConflicts)) {
                        $canBeAccommodated = true;
                    } else if (!empty($reservationConflicts) && empty($errors)) {
                        // Re-map loaned equipment with new ids
                        foreach ($equipmentRequests as $i => $equipmentRequest) {
                            $loanedEquipments[$i]->setEquipmentId($equipmentRequest->getEquipmentId());
                            $Session->getLoanedEquipmentMapper()->uowUpdate($loanedEquipments[$i]);
                        }
                        $canBeAccommodated = true;
                    }

                    if ($canBeAccommodated) {
                        $reservationWasAccommodated = true;
                        $waitingReservation->setIsWaited(false);
                        $Session->getReservationMapper()->uowUpdate($waitingReservation);
                        $Session->getReservationMapper()->commit();
                        break;
                    }
                }

                // Refresh the list
                $waitList = $Session->getReservationManager()->getOrderedWaitingReservations();
            } while ($reservationWasAccommodated);

            return true;
        }

        /**
         * Resolves conflicts into user errors.
         *
         * @param ReservationConflict[] $reservationConflicts from the attempted reservation booking.
         * @param EquipmentRequest[] $equipmentRequests for the reservation.
         *
         * @return String[] errors from attempt to assign alternate equipment id.
         */
        public function assignAlternateEquipmentId($reservationConflicts, &$equipmentRequests)
        {
            $errors = [];

            // No conflicts, so return
            if (empty($reservationConflicts)) {
                return $errors;
            }

            // Attempt to resolve equipment conflicts
            foreach ($reservationConflicts as $reservationConflict) {

                // Log time conflicts
                foreach ($reservationConflict->getDateTimes() as $timeConflict) {
                    $errors[] = "Conflict with time: " . $timeConflict;
                }

                // Time conflicts exist, skip equipment conflict checks
                if (!empty($errors)) {
                    continue;
                }

                // Attempt re-assignment of equipment ids
                foreach ($reservationConflict->getEquipments() as $equipmentConflict) {
                    foreach ($equipmentRequests as $equipmentRequest) {
                        if ($equipmentConflict->getEquipmentId() == $equipmentRequest->getEquipmentId()) {
                            $availableEquipmentIds = $this->_reservationManager->findAvailableEquipmentIds($equipmentRequest->getEquipmentType());
                            if (count($availableEquipmentIds) >= 1) {
                                $equipmentRequest->setEquipmentId($availableEquipmentIds[0]);
                            } else {
                                $this->noAlternativeError($equipmentRequest, $errors);
                            }
                        }
                    }
                }
            }

            return $errors;
        }

        /**
         * Add an error that no alternative equipment could be found.
         *
         * @param EquipmentRequest $equipmentRequest from the attempted reservation booking.
         * @param String[] $errors to add.
         *
         * @return void
         */
        private function noAlternativeError(&$equipmentRequest, &$errors)
        {
            $equipmentType = 'Unknown';

            // This is awful, but would require a refactor in the database
            if ($equipmentRequest->getEquipmentType() == EquipmentType::Computer) {
                $equipmentType = 'Computer';
            } else if ($equipmentRequest->getEquipmentType() == EquipmentType::Projector) {
                $equipmentType = 'Projector';
            }

            $errors[] = "No alternative " . $equipmentType . " could be found for requested id "
                . $equipmentRequest->getEquipmentId();
        }
    }
}