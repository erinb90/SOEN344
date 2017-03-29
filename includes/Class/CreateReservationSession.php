<?php
namespace Stark {

    use Stark\Enums\EquipmentType;
    use Stark\Mappers\LoanContractMapper;
    use Stark\Mappers\LoanedEquipmentMapper;
    use Stark\Mappers\ReservationMapper;
    use Stark\Models\EquipmentRequest;
    use Stark\Models\User;
    use Stark\Utilities\ReservationConflict;
    use Stark\Utilities\ReservationManager;

    class CreateReservationSession
    {
        /**
         * @var \Stark\Models\User $_User The user that the session belongs to.
         */
        private $_user;

        /**
         * @var int $_roomId The unique Id for the room to be reserved.
         */
        private $_roomId;

        /**
         * @var string $_startTimeDate The start time for the room reservation.
         */
        private $_startTimeDate;

        /**
         * @var string $_endTimeDate The end time for the room reservation.
         */
        private $_endTimeDate;

        /**
         * @var string $_title The title of the reservation.
         */
        private $_title;

        /**
         * @var int $_repeats The number of repeating time blocks.
         */
        private $_repeats;

        /**
         * @var EquipmentRequest[] $_equipmentRequests The equipmentRequests for the reservation.
         */
        private $_equipmentRequests;

        /**
         * @var ReservationManager $_reservationManager to check for reservation conflicts.
         */
        private $_reservationManager;

        /**
         * @var array $_errors The errors generated by the reservation sessions.
         */
        private $_errors;

        /**
         * Creates a new reservation session for the user with the supplied parameters.
         *
         * @param \Stark\Models\User $user The user that the session belongs to.
         * @param int $roomId The unique Id for the room to be reserved.
         * @param string $startTimeDate The start time for the room reservation.
         * @param string $endTimeDate The end time for the room reservation.
         * @param string $title The title of the reservation.
         * @param int $repeats The number of times to repeat the reservation.
         * @param EquipmentRequest[] $equipmentRequests (optional) The equipment requests for the reservation.
         */
        public function __construct(User $user, $roomId, $startTimeDate, $endTimeDate, $title, $repeats, $equipmentRequests = [])
        {
            $this->_user = $user;
            $this->_roomId = $roomId;
            $this->_startTimeDate = $startTimeDate;
            $this->_endTimeDate = $endTimeDate;
            $this->_title = $title;
            $this->_equipmentRequests = $equipmentRequests;
            $this->_repeats = $repeats;
            $this->_errors = [];
            $this->_reservationManager = new ReservationManager();
        }

        public function getErrors()
        {
            return $this->_errors;
        }

        public function setError($error)
        {
            $this->_errors[] = $error;
        }

        /**
         * @return boolean returns true if the reservation was successful with no time conflicts.
         *
         */
        public function reserve()
        {
            $repeatedDates = Utilities::getDateRepeats($this->_startTimeDate, $this->_endTimeDate, $this->_repeats);
            $maxRepeats = CoreConfig::settings()['reservations']['max_repeats'];
            if (isset($maxRepeats)) {
                if (count($repeatedDates) > 3) {
                    $this->setError("Cannot repeat reservation more than 3 times.");
                    return false;
                }
            }

            // TODO : Is it all or nothing? Or should we allow non-conflicting repeats to be scheduled?
            $isWaiting = !$this->validateRepeats($repeatedDates);

            // Create a repeated reservation based on the date repeats
            $reservationMapper = new ReservationMapper();

            foreach ($repeatedDates as $i => $date) {
                try {
                    // Create a pending reservation
                    $reservation = $reservationMapper->createReservation(
                        $this->_user->getUserId(),
                        $this->_roomId,
                        $date['start'],
                        $date['end'],
                        $this->_title,
                        $isWaiting);

                    // Add it to the unit of work
                    $reservationMapper->uowInsert($reservation);

                    // Commit the unit of work
                    $reservationMapper->commit();

                    // Create a loan contract Id and associate request equipment
                    if (!empty($this->_equipmentRequests)) {
                        $loanContractId = $this->associateLoanContract($reservation->getReservationID());
                        $this->associateLoanedEquipment($loanContractId, $this->_equipmentRequests);
                    }

                } catch (\Exception $e) {
                    $this->setError($e->getMessage());
                }
            }

            return !$isWaiting;
        }

        /**
         * Validates that the requested dates cause no time conflicts with other reservations.
         *
         * @param \DateTime[] $repeatedDates The repeated dates for the reservation.
         * @return boolean The validation result.
         */
        private function validateRepeats($repeatedDates)
        {
            foreach ($repeatedDates as $repeatedDate) {

                $startTimeDate = $repeatedDate['start'];
                $endTimeDate = $repeatedDate['end'];

                $reservationConflicts = $this->_reservationManager
                    ->checkForConflicts(-1, $this->_roomId, $startTimeDate, $endTimeDate, $this->_equipmentRequests);

                $this->resolveConflicts($reservationConflicts, $this->_errors);
            }

            return empty($this->getErrors());
        }

        /**
         * Resolves conflicts into user errors.
         *
         * @param ReservationConflict[] $reservationConflicts from the attempted reservation booking.
         * @param String[] $errors to add.
         *
         * @return void
         */
        private function resolveConflicts($reservationConflicts, &$errors)
        {
            // No conflicts, so return
            if (empty($reservationConflicts)) {
                return;
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
                    foreach ($this->_equipmentRequests as $equipmentRequest) {
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

        /**
         * Creates loan contract for reservation with the requested equipmentIds.
         *
         * @param int $reservationId The reservationId to associate with the loan contract.
         * @return int The id of the new loan contract, or -1 if the contract creation failed.
         */
        private function associateLoanContract($reservationId)
        {
            $loanContractMapper = new LoanContractMapper();

            try {
                $loanContract = $loanContractMapper->createLoanContract($reservationId);

                // Add it to the unit of work
                $loanContractMapper->uowInsert($loanContract);

            } catch (\Exception $e) {
                $this->setError($e->getMessage());
                return -1;
            }

            // Commit the unit of work
            $loanContractMapper->commit();

            if ($loanContract->getLoanContractiD() == null) {
                return -1;
            }

            return $loanContract->getLoanContractiD();
        }

        /**
         * Creates loan contract for reservation with the requested equipmentIds.
         *
         * @param int $loanContractId The loanContractId to associate with the loan contract.
         * @param EquipmentRequest[] $equipmentRequests The equipment requests to associate with the loan contract.
         * @return void
         */
        private
        function associateLoanedEquipment($loanContractId, $equipmentRequests)
        {
            $loanedEquipmentMapper = new LoanedEquipmentMapper();

            foreach ($equipmentRequests as $equipmentRequest) {
                try {
                    $equipmentId = $equipmentRequest->getEquipmentId();
                    $loanedEquipment = $loanedEquipmentMapper->createLoanedEquipment($loanContractId, $equipmentId);

                    // Add it to the unit of work
                    $loanedEquipmentMapper->uowInsert($loanedEquipment);

                } catch (\Exception $e) {
                    $this->setError($e->getMessage());
                }
            }

            // Commit the units of work
            $loanedEquipmentMapper->commit();
        }
    }
}