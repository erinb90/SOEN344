<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 2:06 AM
 */

namespace Stark\Mappers
{


    use Stark\Interfaces\AbstractMapper;
    use Stark\Interfaces\DomainObject;
    use Stark\Models\LoanedEquipment;
    use Stark\TDG\LoanedEquipmentTDG;

    /**
     * Mapper for LoanedEquipment objects
     * Interacts with the LoanedEquipmentTDG to retrieve and manipulate LoanedEquipment objects from DB
     *
     * @package Stark\Mappers
     */
    class LoanedEquipmentMapper extends AbstractMapper
    {

        /**
         * @var \Stark\TDG\LoanedEquipmentTDG
         */
        private $_tdg;

        /**
         * LoanedEquipmentMapper constructor.
         */
        public function __construct()
        {
            $this->_tdg = new LoanedEquipmentTDG("loaned_equipment", "LoanContractId");
        }



        /**
         * @return \Stark\TDG\LoanedEquipmentTDG
         */
        public function getTdg()
        {
            return $this->_tdg;
        }


        /**
         * Returns all equipment under a certain LoanContract, given its LoanContractId
         *
         * @param $LoanContractId
         *
         * @return array
         */
        public function findEquipmentByContractId($LoanContractId)
        {
            $dbEntries = $this->getTdg()->findEquipmentByContractId($LoanContractId);
            $equipment = [];
            foreach ($dbEntries as $row)
            {
                $equipment[] = $this->getModel($row);
            }
            return $equipment;

        }
        /**
         * Creates a LoanedEquipment object from a DB entry
         *
         * @param $data array data retrieve from the tdg
         *
         * @return LoanedEquipment returns a fully-dressed object
         */
        public function getModel(array $data = null)
        {
            if (!$data)
            {
                return NULL;
            }

            $LoanedEquipment = new LoanedEquipment();
            $LoanedEquipment->setEquipmentId($data['EquipmentId']);
            $LoanedEquipment->setLoanContractId($data['LoanContractId']);

            return $LoanedEquipment;

        }

        /**
         * Creates a LoanedEquipment object given a loanContractId and equipmentId
         *
         * @param int $loanContractId The loanContractId to associate with the loaned equipment.
         * @param int $equipmentId The equipmentId to associate with the loaned equipment.
         *
         * @return \Stark\Models\LoanedEquipment The newly created loaned equipment entry.
         */
        public function createLoanedEquipment($loanContractId, $equipmentId)
        {
            $LoanedEquipment = new LoanedEquipment();
            $LoanedEquipment->setLoanContractId($loanContractId);
            $LoanedEquipment->setEquipmentId($equipmentId);
            return $LoanedEquipment;
        }
    }
}