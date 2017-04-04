<?php
namespace Stark {

    use Stark\Mappers\LoanContractMapper;
    use Stark\Mappers\LoanedEquipmentMapper;
    use Stark\Mappers\ReservationMapper;
    use Stark\RequestModels\EquipmentRequest;
    use Stark\RequestModels\ReservationRequest;
    use Stark\RequestModels\ReservationRequestBuilder;
    use Stark\Utilities\ReservationConflict;
    use Stark\Utilities\ReservationManager;

    class CreateReservationSession
    {
        const WAITLIST = 0;
        const SUCCESS = 1;
        const ERROR = 2;

        /**
         * @var ReservationRequest $_reservationRequest for the create reservation session.
         */
        private $_reservationRequest;

        /**
         * @var array $_errors The errors generated by the reservation sessions.
         */
        private $_errors;

        /**
         * @var int $_waitListPosition of the user's reservation.
         */
        private $_waitListPosition;

        /**
         * @var ReservationManager $_reservationManager to check for reservation conflicts.
         */
        private $_reservationManager;

        /**
         * @var ReservationMapper $_reservationMapper to create a new reservation.
         */
        private $_reservationMapper;

        /**
         * Creates a new reservation session for the user with the reservation request.
         *
         * @param ReservationRequest $reservationRequest for the session.
         */
        public function __construct($reservationRequest)
        {
            $this->_reservationRequest = $reservationRequest;
            $this->_errors = [];
            $this->_waitListPosition = -1;
            $this->_reservationManager = new ReservationManager();
            $this->_reservationMapper = new ReservationMapper();
        }

        /**
         * @return string[] of errors for the create reservation session.
         */
        public function getErrors()
        {
            return $this->_errors;
        }

        /**
         * @param string $error to add the reservation session's error list.
         */
        public function setError($error)
        {
            $this->_errors[] = $error;
        }

        /**
         * @return int position in wait list, or -1 if not in wait list.
         */
        public function getWaitListPosition()
        {
            return $this->_waitListPosition;
        }

        /**
         * Attempts to create a reservation for the user's reservation request.
         *
         * @return int returns status code for reservation (0 wait list, 1 success, 2 error).
         */
        public function reserve()
        {
            $repeatedDates = $this->getReservationRecurrenceDates();
            $recurrencesWithinLimit = $this->validateRecurrences($repeatedDates);
            $totalBookingTimeWithinLimit =
                $this->_reservationManager->validateMaxBookingTimePerWeek($this->_reservationRequest, $this->_errors);

            if (!$recurrencesWithinLimit || !$totalBookingTimeWithinLimit) {
                return self::ERROR;
            }

            $isWaiting = !$this->validateRepeats($repeatedDates);

            foreach ($repeatedDates as $i => $date) {
                try {
                    // Create a pending reservation
                    $reservation = $this->_reservationMapper->createReservation(
                        $this->_reservationRequest->getUserId(),
                        $this->_reservationRequest->getRoomId(),
                        $date['start'],
                        $date['end'],
                        $this->_reservationRequest->getTitle(),
                        $isWaiting);

                    // Add it to the unit of work
                    $this->_reservationMapper->uowInsert($reservation);

                    // Commit the unit of work
                    $this->_reservationMapper->commit();
                    $this->_reservationRequest->setReservationId($reservation->getReservationID());

                    // Create a loan contract Id and associate request equipment
                    $equipmentRequests = $this->_reservationRequest->getEquipmentRequests();
                    if (!empty($equipmentRequests)) {
                        $loanContractId = $this->associateLoanContract($reservation->getReservationID());
                        $this->associateLoanedEquipment($loanContractId, $equipmentRequests);
                    }

                    if ($isWaiting) {
                        $this->_waitListPosition = $this->_reservationManager->getWaitListPosition($reservation->getReservationID());
                    }

                } catch (\Exception $exception) {
                    $this->setError($exception->getMessage());
                }
            }

            return $isWaiting ? self::WAITLIST : self::SUCCESS;
        }

        /**
         * @return array of reservation recurrence dates.
         */
        private function getReservationRecurrenceDates()
        {
            $startTimeDate = $this->_reservationRequest->getStartTimeDate();
            $endTimeDate = $this->_reservationRequest->getEndTimeDate();
            $recurrences = $this->_reservationRequest->getRecurrences();
            return Utilities::getDateRepeats($startTimeDate, $endTimeDate, $recurrences);
        }

        /**
         * Validates the number of reservation recurrences.
         *
         * @param $repeatedDates
         * @return boolean if the recurrences respect the maximum recurrence limit.
         */
        private function validateRecurrences($repeatedDates)
        {
            $maxRecurrences = $this->_reservationRequest->getMaxRecurrences();
            if (!isset($maxRecurrences)) {
                $this->setError("Max recurrences not set in configuration.");
                return false;
            }

            if (count($repeatedDates) > $maxRecurrences) {
                $this->setError("Cannot repeat reservation more than " . $maxRecurrences . " times.");
                return false;
            }

            return true;
        }

        /**
         * Validates that the requested dates cause no time conflicts with other reservations.
         *
         * @param string[] $repeatedDates The repeated dates for the reservation.
         * @return boolean The validation result.
         */
        private function validateRepeats($repeatedDates)
        {
            foreach ($repeatedDates as $repeatedDate) {

                $startTimeDate = $repeatedDate['start'];
                $endTimeDate = $repeatedDate['end'];
                $roomId = $this->_reservationRequest->getRoomId();
                $equipmentRequests = $this->_reservationRequest->getEquipmentRequests();

                // Needs to be done because there is a separate reservation request for each
                // recurrence
                $reservationRequestBuilder = new ReservationRequestBuilder();
                $reservationRequestBuilder
                    ->roomId($roomId)
                    ->startTimeDate($startTimeDate)
                    ->endTimeDate($endTimeDate)
                    ->equipmentRequests($equipmentRequests);
                $reservationRequest = $reservationRequestBuilder->build();

                if (!$this->validateNewReservation($reservationRequest)) {
                    return false;
                }
            }
            return true;
        }

        /**
         * @param ReservationRequest $reservationRequest
         * @return bool specifying if the new reservation can be accommodated
         *
         * Checks for time and equipment conflicts, returns true if new res can be accommodated
         */
        public function validateNewReservation($reservationRequest)
        {
            // Check for conflicts
            $reservationConflicts = $this->_reservationManager
                ->checkForConflicts($reservationRequest);

            $errors = $this->_reservationManager->convertConflictsToErrors($reservationConflicts);

            foreach ($errors as $error) {
                $this->_errors[] = $error;
            }

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

            $equipmentRequests = $reservationRequest->getEquipmentRequests();

            // There were time conflicts
            if ($hasTimeConflicts) {
                return false;
            } else if ($hasEquipmentConflicts) {
                $equipmentReassignmentErrors = $this->_reservationManager->assignAlternateEquipmentId($reservationConflicts, $equipmentRequests);

                foreach ($equipmentReassignmentErrors as $error) {
                    $this->_errors[] = $error;
                }

                // There were unresolved equipment conflicts
                if (!empty($equipmentReassignmentErrors)) {
                    return false;
                }
            }

            return true;
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
        private function associateLoanedEquipment($loanContractId, $equipmentRequests)
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