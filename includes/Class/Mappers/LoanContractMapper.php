<?php

namespace Stark\Mappers
{

    use Stark\Interfaces\AbstractMapper;
    use Stark\Models\LoanContract;
    use Stark\TDG\LoanContractTDG;

    /**
     * Class LoanContractMapper
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
         * returns LoanContract based on Reservation
         * @param $reservationid
         *
         * @return \Stark\Models\LoanContract
         */
        public function findByReservationId($reservationid)
        {
            $loanContractArray = $this->getTdg()->findByReservationId($reservationid)[0];
            if(!isset($loanContract)){
                $loanContract = new LoanContract();
                $loanContract->setLoanContractiD($loanContractArray['LoanContractId']);
                $loanContract->setReservationId($loanContractArray['ReservationId']);
                return $loanContract;
            }

            return $loanContract;
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