<?php

namespace Stark\Models
{


    use Stark\Interfaces\DomainObject;

    /**
     * Class LoanedEquipment
     * @package Stark\Models
     */
    class LoanedEquipment implements DomainObject
    {

        /**
         * @var int
         */
        private $_LoanedEquipmentId;

        /**
         * @var
         */
        private $_LoanContractId;

        /**
         * @var
         */
        private $_EquipmentId;

        /**
         * @return mixed
         */
        public function getLoanedEquipmentId()
        {
            return $this->_LoanedEquipmentId;
        }

        /**
         * @param mixed $LoanedEquipmentId
         */
        public function setLoanedEquipmentId($LoanedEquipmentId)
        {
            $this->_LoanedEquipmentId = $LoanedEquipmentId;
        }

        /**
         * @return mixed
         */
        public function getLoanContractId()
        {
            return $this->_LoanContractId;
        }

        /**
         * @param mixed $LoanContractId
         */
        public function setLoanContractId($LoanContractId)
        {
            $this->_LoanContractId = $LoanContractId;
        }

        /**
         * @return mixed
         */
        public function getEquipmentId()
        {
            return $this->_EquipmentId;
        }

        /**
         * @param mixed $EquipmentId
         */
        public function setEquipmentId($EquipmentId)
        {
            $this->_EquipmentId = $EquipmentId;
        }
    }
}