<?php

namespace Stark\Utilities;

use Stark\Interfaces\Equipment;
use Stark\Mappers\LoanContractMapper;
use Stark\Mappers\LoanedEquipmentMapper;

class EquipmentManager
{
    /**
     * @var \Stark\Mappers\LoanContractMapper $_loanContractMapper to retrieve loan contracts
     */
    private $_loanContractMapper;

    /**
     * @var \Stark\Mappers\LoanedEquipmentMapper $_loanedEquipmentMapper to retrieve loaned equipment
     */
    private $_loanedEquipmentMapper;

    /**
     * EquipmentManager constructor.
     */
    public function __construct()
    {
        $this->_loanContractMapper = new LoanContractMapper();
        $this->_loanedEquipmentMapper = new LoanedEquipmentMapper();
    }

    /**
     * Finds equipment associated with a reservation.
     *
     * @param int $reservationId to query
     *
     * @return Equipment[] of equipment or empty array if none
     */
    public function findEquipmentForReservation($reservationId)
    {
        if (!isset($reservationId)) {
            return [];
        }

        $loanContract = $this->_loanContractMapper->findByReservationId($reservationId);
        if ($loanContract == null) {
            return [];
        }

        return $this->findEquipmentForLoanContract($loanContract->getLoanContractiD());
    }

    /**
     * Finds equipment associated with a loan contract.
     *
     * @param int $loanContractId to query
     *
     * @return Equipment[] of equipment or empty array if none
     */
    private function findEquipmentForLoanContract($loanContractId)
    {
        if (!isset($loanContractId)) {
            return [];
        }

        return $this->_loanedEquipmentMapper->findEquipmentByContractId($loanContractId);
    }
}