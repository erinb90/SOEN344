<?php
namespace Stark {

    use Stark\Mappers\LoanedEquipmentMapper;
    use Stark\Mappers\ReservationMapper;
    use Stark\Models\Reservation;
    use Stark\Utilities\ReservationManager;


    /**
     * Class DeleteReservationSession
     * @package Stark
     */
    class DeleteReservationSession
    {
        private $_reservationId;

        /**
         * @var ReservationManager $_reservationManager to check for reservation conflicts.
         */
        private $_reservationManager;

        private $_ReservationMapper;

        /**
         * @var LoanedEquipmentMapper $_loanedEquipmentMapper to update loaned equipment.
         */
        private $_loanedEquipmentMapper;

        /***
         * DeleteReservationSession constructor.
         *
         * @param $reservationId
         */
        private function __construct($reservationId)
        {
            $this->_reservationId = $reservationId;
            $this->_reservationManager = new ReservationManager();
            $this->_ReservationMapper = new ReservationMapper();
            $this->_loanedEquipmentMapper = new LoanedEquipmentMapper();
        }

        /**
         * @return \Stark\Models\Reservation|null
         */
        public function getReservation()
        {
            /**
             * @var $Reservation Reservation
             */
            $Reservation = $this->_ReservationMapper->findByPk($this->_reservationId);

            return $Reservation;
        }

        /**
         * @return \Stark\Mappers\ReservationMapper
         */
        public function getReservationMapper()
        {
            return $this->_ReservationMapper;
        }

        /**
         * @return \Stark\Mappers\LoanedEquipmentMapper
         */
        public function getLoanedEquipmentMapper()
        {
            return $this->_loanedEquipmentMapper;
        }

        /**
         * @return \Stark\Utilities\ReservationManager
         */
        public function getReservationManager()
        {
            return $this->_reservationManager;
        }

        /**
         * @param $reservationId
         *
         * @return bool
         */
        public static function delete($reservationId)
        {
            $Session = new DeleteReservationSession($reservationId);
            $currentReservation = $Session->getReservation();

            // Delete the reservation
            $Session->getReservationMapper()->uowDelete($currentReservation);
            $Session->getReservationMapper()->commit();

            $Session->getReservationManager()->accommodateReservations();

            return true;
        }
    }
}