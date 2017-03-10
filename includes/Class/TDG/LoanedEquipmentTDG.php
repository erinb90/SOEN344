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
     * @package Stark\TDG
     */
    class LoanedEquipmentTDG extends TDG

    {

        public function findEquipmentByContractId($LoanContractId)
        {
            return $this->query('*', ['LoanContractId' => $LoanContractId]);
        }

        /**
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanedEquipment $object
         *
         * @return int returns the last inserted id
         */
        public function insert(DomainObject &$object)
        {
            Registry::getConnection()->beginTransaction();
            $lastId = -1;
            try
            {
                Registry::getConnection()->insert($this->getTable(),
                    [
                        "LoanContractId" => $object->getLoanContractId(),
                        "EquipmentId" => $object->getEquipmentId()
                    ]
                );

                $lastId = Registry::getConnection()->lastInsertId();
                Registry::getConnection()->commit();

            }
            catch (\Exception $e)
            {
                Registry::getConnection()->rollBack();
            }

            return $lastId;
        }

        /**
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanedEquipment $object
         *
         * @return mixed
         */
        public function delete(DomainObject &$object)
        {
            return Registry::getConnection()->delete($this->getTable(),
                [
                    $this->getPk() => $object->getLoanContractId()
                ]
            );
        }

        /**
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanedEquipment $object
         *
         * @return bool
         */
        public function update(DomainObject &$object)
        {
            try
            {
                Registry::getConnection()->update(
                    $this->getTable(),
                    [
                        "EquipmentId" => $object->getEquipmentId(),
                        "Quantity"    => $object->getQuantity()
                    ],
                    [$this->getPk() => $object->getLoanContractId()]
                );

                return TRUE;
            }
            catch (\Exception $e)
            {

            }

            return FALSE;

        }
    }
}