<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 2:05 AM
 */

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
         * @var
         */
        private $_LoanContractId;

        /**
         * @var
         */
        private $_EquipmentId;

        /**
         * @var
         */
        private $_Quantity;

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

        /**
         * @return mixed
         */
        public function getQuantity()
        {
            return $this->_Quantity;
        }

        /**
         * @param mixed $Quantity
         */
        public function setQuantity($Quantity)
        {
            $this->_Quantity = $Quantity;
        }


    }
}