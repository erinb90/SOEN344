<?php

namespace Stark\Models
{

    use Stark\Interfaces\DomainObject;

    /**
     * Class LoanContract
     * @package Stark\Models
     */
    class LoanContract implements DomainObject
    {

        /**
         * @var int
         */
        private $_LoanContractiD;

        /**
         * @var int
         */
        private $_ReservationId;


        /**
         * LoanContract constructor.
         */
        public function __construct()
        {

        }

        /**
         * @return int
         */
        public function getLoanContractiD()
        {
            return $this->_LoanContractiD;
        }

        /**
         * @param int $LoanContractiD
         *
         */
        public function setLoanContractiD($LoanContractiD)
        {
            $this->_LoanContractiD = $LoanContractiD;
        }

        /**
         * @return mixed
         */
        public function getReservationId()
        {
            return $this->_ReservationId;
        }

        /**
         * @param mixed $ReservationId
         *
         */
        public function setReservationId($ReservationId)
        {
            $this->_ReservationId = $ReservationId;
        }


    }
}