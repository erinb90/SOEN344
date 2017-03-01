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
    }
}