<?php

namespace Stark\Mappers
{

    use Stark\Interfaces\AbstractMapper;
    use Stark\Models\LoanContract;
    use Stark\TDG\LoanContractTDG;

    /**
     * Mapper for LoanContract objects
     * Interacts with the LoanContractTDG to retrieve and manipulate LoanContract objects from DB
     *
     * @package Stark\Mappers
     */
    class LoanContractMapper extends AbstractMapper
    {

        /**
         * @var \Stark\TDG\LoanContractTDG
         */
        private $_tdg;


        public function __construct()
        {
            $this->_tdg = new LoanContractTDG("loan_contract", "LoanContractId");
        }

        /**
         * @return \Stark\TDG\LoanContractTDG
         */
        public function getTdg()
        {
            return $this->_tdg;
        }

        /**
         * Creates a LoanContract object from a DB entry
         *
         * @param $data array data retrieve from the tdg
         *
         * @return LoanContract returns a fully-dressed object
         */
        public function getModel(array $data = null)
        {
            if (!$data)
            {
                return NULL;
            }

            $LoanContract = new LoanContract();
            $LoanContract->setLoanContractiD($data['LoanContractId']);
            $LoanContract->setReservationId($data['ReservationId']);

            return $LoanContract;

        }

        /**
         * Returns a LoanContract given its ReservationId
         *
         * @param $reservationid
         *
         * @return \Stark\Models\LoanContract
         */
        public function findByReservationId($reservationid)
        {
            $tdgResult = $this->getTdg()->findByReservationId($reservationid);
            if(isset($tdgResult) && !empty($tdgResult)){
                $loanContractArray = $tdgResult[0];
                return $this->getModel($loanContractArray);
            }

            return null;
        }

        /**
         * Creates a loan contract with a reservationId.
         * @param int $reservationId The reservationId to associate with the loan contract.
         *
         * @return \Stark\Models\LoanContract The newly created loan contract.s
         */
        public function createLoanContract($reservationId)
        {
            $LoanContract = new LoanContract();
            $LoanContract->setReservationId($reservationId);
            return $LoanContract;
        }
    }
}