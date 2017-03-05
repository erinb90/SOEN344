<?php
namespace Stark
{

    use Stark\Mappers\ReservationMapper;
    use Stark\Models\Reservation;


    /**
     * Class DeleteReservationSession
     * @package Stark
     */
    class DeleteReservationSession
    {
        private $_reservationId;


        private $_ReservationMapper;

        /***
         * DeleteReservationSession constructor.
         *
         * @param $reservationId
         */
        private function __construct($reservationId)
        {
            $this->_reservationId = $reservationId;
            $this->_ReservationMapper = new ReservationMapper();
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
         * @param $reservationId
         *
         * @return bool
         */
        public static function delete($reservationId)
        {


            $Session = new DeleteReservationSession($reservationId);

            $Wailist = new Waitlist($Session->getReservation()->getRoomId(), $Session->getReservation()->getStartTimeDate(), $Session->getReservation()->getEndTimeDate());

            $NextReservation = $Wailist->getNextReservationWaiting();



            if($NextReservation)
            {
                $NextReservation->setIsWaited(false);
                $Session->getReservationMapper()->uowUpdate($NextReservation);
            }


            $CurrentReservation = $Session->getReservation();

            $Session->getReservationMapper()->uowDelete($CurrentReservation);

            return $Session->getReservationMapper()->commit();


        }

    }
}