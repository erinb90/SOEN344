<?php

namespace Stark\Utilities;

use Stark\Interfaces\Equipment;
use Stark\Mappers\ComputerMapper;
use Stark\Mappers\LoanContractMapper;
use Stark\Mappers\LoanedEquipmentMapper;
use Stark\Mappers\ProjectorMapper;
use Stark\Models\LoanedEquipment;

class EquipmentManager
{
    /**
     * @var \Stark\Mappers\LoanContractMapper $_loanContractMapper to retrieve loan contracts
     */
    private $_loanContractMapper;

    /**
     * @var \Stark\Mappers\ComputerMapper $_computerMapper to retrieve computers
     */
    private $_computerMapper;

    /**
     * @var \Stark\Mappers\ProjectorMapper $_projectorMapper to retrieve projectors
     */
    private $_projectorMapper;

    /**
     * EquipmentManager constructor.
     */
    public function __construct()
    {
        $this->_loanContractMapper = new LoanContractMapper();
        $this->_loanedEquipmentMapper = new LoanedEquipmentMapper();
        $this->_computerMapper = new ComputerMapper();
        $this->_projectorMapper = new ProjectorMapper();
    }

    /**
     * Gets all available equipment in the system.
     *
     * @return Equipment[] of all equipment in the system or empty array if none
     */
    public function getAllEquipment()
    {
        /**
         * @var Equipment[] $equipment
         */
        $equipment = array_merge($this->_computerMapper->findAll(), $this->_projectorMapper->findAll());
        return $equipment;
    }

    /**
     * Gets equipment based on id.
     *
     * @param int $equipmentId for the requested equipment.
     *
     * @return Equipment equipment in the system or null if not found
     */
    public function getEquipmentForId($equipmentId)
    {
        /**
         * @var Equipment[] $equipments
         */
        $equipments = array_merge($this->_computerMapper->findAll(), $this->_projectorMapper->findAll());
        foreach ($equipments as $equipment){
            if($equipment->getEquipmentId() == $equipmentId){
                return $equipment;
            }
        }

        return null;
    }

    /**
     * Finds equipment associated with a reservation.
     *
     * @param int $reservationId to query
     *
     * @return LoanedEquipment[] of equipment or empty array if none
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
     * @return LoanedEquipment[] associated equipment or empty array if none
     */
    private function findEquipmentForLoanContract($loanContractId)
    {
        if (!isset($loanContractId)) {
            return [];
        }

        return $this->_loanedEquipmentMapper->findEquipmentByContractId($loanContractId);
    }
}