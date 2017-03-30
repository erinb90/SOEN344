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

            // Delete the reservation
            $Session->getReservationMapper()->uowDelete($currentReservation);
            $Session->getReservationMapper()->commit();

            // No next reservation
            if (empty($waitList)) {
                return false;
            }

            do {
                // do-while end condition to indicate when no additional wait listed reservations
                // can be changed to confirmed
                $reservationWasAccommodated = false;

                // Go through the wait list
                foreach ($waitList as $waitingReservation) {
                    // If the current reservation was accommodated
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
                        ->checkForConflicts($waitingReservation->getReservationID(), $waitingReservation->getRoomId(), $waitingReservation->getStartTimeDate(), $waitingReservation->getEndTimeDate(), $equipmentRequests);

                    // If required
                    $errors = $Session->getReservationManager()
                        ->assignAlternateEquipmentId($reservationConflicts, $equipmentRequests);

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

                    // Update the reservation status to active
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
    }
}