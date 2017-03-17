<?php
/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-02-16
 * Time: 4:26 PM
 */

namespace Stark\TDG
{

    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Registry;

    /**
     * Class LoanContractTDG
     * Performs DB calls for LoanContract table
     * @package Stark\TDG
     */
    class LoanContractTDG extends TDG
    {
        /**
         * Find a LoanContract in DB given its reservationId
         * @param $reservationId
         * @return array
         */
        public function findByReservationId($reservationId)
        {
            return $this->query('*',["ReservationId" => $reservationId]);
        }

        /**
         * Insert a LoanContract object in DB
         *
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanContract $object
         *
         * @return int
         */
        public function insert(DomainObject &$object)
        {
            Registry::getConnection()->beginTransaction();
            $lastId = -1;
            try
            {
                Registry::getConnection()->insert($this->getTable(),
                    [
                        "ReservationId" => $object->getReservationId()
                    ]
                );

                $lastId = Registry::getConnection()->lastInsertId();
                $object->setLoanContractiD($lastId);
                Registry::getConnection()->commit();

            }
            catch (\Exception $e)
            {
                Registry::getConnection()->rollBack();
            }

            return $lastId;
        }

        /**
         * Deletes a LoanContract object from DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanContract $object
         *
         * @return bool
         */
        public function delete(DomainObject &$object)
        {
            try
            {
                Registry::getConnection()->delete($this->getTable(),
                    [
                        $this->getPk() => $object->getLoanContractiD()
                    ]
                );
                return true;
            }
            catch(\Exception $e)
            {

            }
            return false;
        }

        /**
         * Updates a LoanContract object in DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanContract $object
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

                        "ReservationId" => $object->getReservationId()
                    ],
                    [$this->getPk() => $object->getLoanContractiD()]
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