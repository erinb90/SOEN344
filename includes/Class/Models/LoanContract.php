<?php
/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-02-16
 * Time: 4:36 PM
 */

namespace Stark\Models
{
    use Stark\Interfaces\DomainObject;

    class LoanContract implements DomainObject
    {

        private $_LoanContractiD;

        private $_ReservationId;

        public function __construct()
        {

        }

        /**
         * @return mixed
         */
        public function getLoanContractiD()
        {
            return $this->_LoanContractiD;
        }

        /**
         * @param mixed $LoanContractiD
         *
         * @return LoanContract
         */
        public function setLoanContractiD($LoanContractiD)
        {
            $this->_LoanContractiD = $LoanContractiD;

            return $this;
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
         * @return LoanContract
         */
        public function setReservationId($ReservationId)
        {
            $this->_ReservationId = $ReservationId;

            return $this;
        }



    }
}