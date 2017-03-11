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

            $waitList = new Waitlist($Session->getReservation()->getRoomId(), $Session->getReservation()->getStartTimeDate(), $Session->getReservation()->getEndTimeDate());

            $NextReservation = $waitList->getNextReservationWaiting();

            // No next reservation
            if (!$NextReservation) {
                return false;
            }

            $canBeAccommodated = false;
            $loanedEquipments = $Session->getReservationManager()->getLoanedEquipmentForReservation($NextReservation->getReservationID());
            if (isset($loanedEquipments) && !empty($loanedEquipments)) {
                /**
                 * @var EquipmentRequest[] $equipmentRequests
                 */
                $equipmentRequests = [];
                foreach ($loanedEquipments as $loanedEquipment) {
                    $equipment = $Session->getReservationManager()->getEquipmentForId($loanedEquipment->getEquipmentId());
                    if ($equipment != null) {
                        $equipmentRequests[] = new EquipmentRequest($equipment->getEquipmentId(), $equipment->getDiscriminator());
                    }
                }

                $reservationConflicts = $Session->getReservationManager()
                    ->checkForConflicts($NextReservation->_roomId, $NextReservation->getStartTimeDate(), $NextReservation->getEndTimeDate(), $equipmentRequests);

                // If required
                $errors = $Session->assignAlternateEquipmentId($reservationConflicts, $equipmentRequests);

                if (empty($errors)) {
                    // Re-map loaned equipment with new ids
                    foreach ($equipmentRequests as $i => $equipmentRequest) {
                        $loanedEquipments[$i]->setEquipmentId($equipmentRequest->getEquipmentId());
                        $Session->getLoanedEquipmentMapper()->uowUpdate($loanedEquipments[$i]);
                    }
                    $canBeAccommodated = true;
                } else {
                    // TODO: Cycle through next reservation
                }
            }

            if ($canBeAccommodated) {
                $NextReservation->setIsWaited(false);
                $Session->getReservationMapper()->uowUpdate($NextReservation);
                $CurrentReservation = $Session->getReservation();
                $Session->getReservationMapper()->uowDelete($CurrentReservation);
                $Session->getReservationMapper()->commit();
            }

            return $canBeAccommodated;
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

                // Get loaned equipments for the reservation
                $reservationId = $reservationConflict->getReservation()->getReservationID();
                $loanedEquipments = $this->getReservationManager()->getLoanedEquipmentForReservation($reservationId);

                // Store already assigned equipment Ids
                $assignedEquipmentIds = [];
                foreach ($loanedEquipments as $assignedEquipment) {
                    $assignedEquipmentIds[] = $assignedEquipment->getEquipmentId();
                }

                // Attempt to re-assign an available equipmentId
                $equipments = $this->getReservationManager()->getAllEquipment();
                foreach ($equipmentRequests as $equipmentRequest) {
                    $newEquipmentIdAssigned = false;
                    foreach ($equipments as $equipment) {
                        // Wrong type, so continue
                        if ($equipmentRequest->getEquipmentType() != $equipment->getDiscriminator()) {
                            continue;
                        }

                        // Checks to see if id is part of the already assigned ids
                        $foundId = in_array($equipment->getEquipmentId(), $assignedEquipmentIds);
                        if (!$foundId) {
                            $equipmentRequest->setEquipmentId($equipment->getEquipmentId());
                            $newEquipmentIdAssigned = true;
                        }

                        // Break, since we have found a new id to assign
                        if ($newEquipmentIdAssigned) {
                            break;
                        }
                    }

                    // Could not assign a new equipment id, reservation needs to be placed on wait list
                    if (!$newEquipmentIdAssigned) {
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

            return $errors;
        }
    }
}