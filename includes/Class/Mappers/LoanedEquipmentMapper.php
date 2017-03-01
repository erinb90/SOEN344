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
     * Class LoanedEquipmentMapper
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


        public function findEquipmentByContractId($LoanContractId)
        {
            $m = $this->getTdg()->findEquipmentByContractId($LoanContractId);
            $equipment = [];
            foreach ($m as $row)
            {
                $equipment[] = $this->getModel($row);
            }
            return $equipment;

        }
        /**
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
            $LoanedEquipment->setQuantity($data['Quantity']);

            return $LoanedEquipment;

        }

        /**
         * Creates a loaned equipment entry with a loanContractId.
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
            // TODO : Remove quantity from database tables
            $LoanedEquipment->setQuantity(1);
            return $LoanedEquipment;
        }
    }
}