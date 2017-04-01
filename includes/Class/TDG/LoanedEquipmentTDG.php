<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 2:07 AM
 */

namespace Stark\TDG
{


    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Registry;

    /**
     * Class LoanedEquipmentTDG
     * Performs DB calls for LoanedEquipment table
     * @package Stark\TDG
     */
    class LoanedEquipmentTDG extends TDG

    {

        /**
         * Find a LoanedEquipment object given its LoanContractId
         * @param $LoanContractId
         * @return array
         */
        public function findEquipmentByContractId($LoanContractId)
        {
            return $this->query('*', ['LoanContractId' => $LoanContractId]);
        }

        /**
         * Insert LoanedEquipment object into DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanedEquipment $object
         *
         * @return int returns the last inserted id
         */
        public function insert(DomainObject &$object)
        {
            Registry::getConnection()->insert($this->getTable(),
                [
                    "LoanContractId" => $object->getLoanContractId(),
                    "EquipmentId" => $object->getEquipmentId()
                ]
            );

            $lastId = Registry::getConnection()->lastInsertId();
            $object->setLoanedEquipmentId($lastId);
        }

        /**
         * Delete LoanedEquipment object from DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanedEquipment $object
         *
         * @return bool
         */
        public function delete(DomainObject &$object)
        {
            Registry::getConnection()->delete($this->getTable(),
                [
                    $this->getPk() => $object->getLoanedEquipmentId()
                ]
            );
        }

        /**
         * Update LoanedEquipment object in DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanedEquipment $object
         *
         * @return bool
         */
        public function update(DomainObject &$object)
        {
            Registry::getConnection()->update(
                $this->getTable(),
                [
                    "EquipmentId" => $object->getEquipmentId()
                ],
                [$this->getPk() => $object->getLoanedEquipmentId()]
            );
        }
    }
}