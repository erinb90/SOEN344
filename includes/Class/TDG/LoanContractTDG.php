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
     * @package Stark\TDG
     */
    class LoanContractTDG extends TDG
    {

        public function findByReservationId($reservationId)
        {
            return $this->query('*',["ReservationId" => $reservationId]);
        }

        /**
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
                Registry::getConnection()->insert($this->getParentTable(),
                    [
                        "ReservationId" => $object->getReservationId()
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
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanContract $object
         *
         * @return mixed
         */
        public function delete(DomainObject &$object)
        {

            return Registry::getConnection()->delete($this->getTable(),
                [
                    $this->getPk() => $object->getLoanContractiD()
                ]
            );
        }

        /**
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\LoanContract $object
         *
         * @return mixed
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