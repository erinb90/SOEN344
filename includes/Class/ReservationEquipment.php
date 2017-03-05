<?php

namespace Stark
{
    use Stark\Mappers\LoanContractMapper;
    use Stark\Mappers\LoanedEquipmentMapper;
    use Stark\Mappers\ProjectorMapper;
    use Stark\Mappers\ReservationMapper;

    use Stark\Models\LoanContract;
    use Stark\Models\LoanedEquipment;
    use Stark\Models\Projector;
    use Stark\Models\Reservation;

    abstract class ReservationEquipment
    {

        private $_reservationId;

        /**
         * @var $_ReservationMapper ReservationMapper
         */
        protected $_ReservationMapper;

        /**
         * @var  LoanContractMapper
         */
        protected $_LoanContractMapper;

        /**
         * @var  LoanedEquipmentMapper
         */
        protected $_LoanedEquipmentMapper;

        /**
         * @var null|Reservation
         */
        protected $_Reservation = null;

        /**
         * @var null|LoanContract
         */
        protected $_LoanContract = null;

        /**
         * ReservationEquipment constructor.
         *
         * @param $reservationId
         */
        public function __construct($reservationId)
        {

            $this->_reservationId = $reservationId;


            $this->_ReservationMapper = new ReservationMapper();
            $this->_LoanContractMapper = new LoanContractMapper();
            $this->_LoanedEquipmentMapper = new LoanedEquipmentMapper();

            $this->findReservation();
            $this->findContract();
        }

        /**
         * @return mixed
         */
        public function getReservationId()
        {
            return $this->_reservationId;
        }

        /**
         *
         */
        private function findReservation()
        {
            $this->_Reservation = $this->_ReservationMapper->findByPk($this->_reservationId);
        }

        /**
         *
         */
        private function findContract()
        {
            $this->_LoanContract = $this->_LoanContractMapper->findByReservationId($this->getReservation()->getReservationID());
        }

        /**
         * @return null|LoanContract
         */
        public function getLoanContract()
        {
            return $this->_LoanContract;
        }
        /**
         * @return Reservation|null
         */
        public function getReservation()
        {
            return $this->_Reservation;
        }

        /**
         * @return bool
         */
        public final function hasContract()
        {
            return $this->getLoanContract() != null;
        }


        /**
         * @param $reservationId
         *
         * @return mixed
         */
        public abstract static function find($reservationId);



    }
}