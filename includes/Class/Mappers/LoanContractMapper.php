<?php

namespace Stark\Mappers
{

    use Stark\Interfaces\AbstractMapper;
    use Stark\Models\LoanContract;
    use Stark\TDG\LoanContractTDG;

    class LoanContractMapper extends AbstractMapper
    {

        /**
         * @var \Stark\TDG\LoanContractTDG
         */
        private $_tdg;


        public function __construct()
        {
            $this->_tdg = new LoanContractTDG();
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
        public function getModel($data)
        {
            if(!$data)
            {
                return null;
            }

            $LoanContract = new LoanContract();
            $LoanContract->setLoanContractiD($data['LoanContractId']);
            $LoanContract->setReservationId($data['ReservationId']);

            return $LoanContract;

        }
    }
}