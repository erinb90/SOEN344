<?php

namespace Stark
{


    use Stark\Mappers\LoanContractMapper;
    use Stark\Mappers\LoanedEquipmentMapper;
    use Stark\Mappers\ReservationMapper;

    use Stark\Models\LoanedEquipment;
    use Stark\Models\Reservation;

    /**
     * Class EquipmentFinder
     * @package Stark
     */
    class EquipmentFinder
    {

        private $_starttime;

        private $_endtime;

        private $_overalappingReservations = [];

        private $_loanedEquipment = [];

        /**
         * EquipmentFinder constructor.
         *
         * @param $starttime
         * @param $endtime
         */
        private function __construct($starttime, $endtime)
        {

            // assumes the dates given are correct and valid etc etc etc
            $this->_starttime = $starttime;
            $this->_endtime = $endtime;


            $this->findSamereservationTimes();
            $this->mapEquipment();

        }

        /**
         *
         */
        private function findSamereservationTimes()
        {
            $ReservationMapper = new ReservationMapper();


            $reservations = $ReservationMapper->findAll();
            $start = strtotime($this->_starttime);
            $end = strtotime($this->_endtime);
            $this->_overalappingReservations = [];


            /**
             * @var $Reservation \Stark\Models\Reservation
             */
            foreach ($reservations as $Reservation)
            {



                if($Reservation->isIsWaited())
                    continue;
                $startTime = strtotime($Reservation->getStartTimeDate());
                $endTime = strtotime($Reservation->getEndTimeDate());
                // was the start time of current reservation between the conflicted start and end time period?
                if ($start >= $startTime && $start < $endTime)
                {
                    $this->_overalappingReservations[$Reservation->getReservationID()]["reservation"] = $Reservation;
                }
                // was the end time of current reservation between the conflicted start and end time period?
                if ($end > $startTime && $end <= $endTime)
                {
                    $this->_overalappingReservations[$Reservation->getReservationID()]["reservation"] = $Reservation;
                }
                // is the current reservation start time less than the one reserved and is the end time of the one reserved less than the current end time
                if ($startTime >= $start && $end >= $endTime)
                {
                    $this->_overalappingReservations[$Reservation->getReservationID()]["reservation"] = $Reservation;
                }


            }



        }


        private function mapEquipment()
        {
            $LoanContractMapper = new LoanContractMapper();
            $LoanedEquipmentMapper = new LoanedEquipmentMapper();

            foreach($this->_overalappingReservations as $reservationData)
            {
                /**
                 * @var $Reservation Reservation
                 */
                $Reservation = $reservationData["reservation"];

                $reservationId = $Reservation->getReservationId();
                // get loan contract
                $LoanedContract = $LoanContractMapper->findByReservationId($reservationId);
                // get equipment data pointer
                $equipmentData = &$this->_overalappingReservations[$reservationId]["equipment"];
                // initialize equipment data
                $equipmentData = [];

                if($LoanedContract != null)
                {
                    $equipment = $LoanedEquipmentMapper->findEquipmentByContractId($LoanedContract->getLoanContractiD());
                    /**
                     * @var $LoandEquipment LoanedEquipment
                     */
                    foreach($equipment as $LoandEquipment)
                    {
                        $equipmentData[] = $LoandEquipment;

                        // keep a copy in state
                        $this->_loanedEquipment[] = $LoandEquipment;
                    }
                }

            }


        }

        /**
         * @param $start
         * @param $end
         *
         * @return \Stark\EquipmentFinder
         */
        public static function find($start, $end)
        {
            return new EquipmentFinder($start,$end);
        }

        /**
         * @return array
         */
        public function getLoanedEquipment()
        {
            return $this->_loanedEquipment;
        }

        /**
         * Returns the number of equipment found in this time slot
         * @return int
         */
        public function numberOfEquipmentFound()
        {
            return count($this->getLoanedEquipment());

        }

        /**
         * Returns true if the equipment id was found in this pile of equipment within specified time frame
         * @param $equipmentId
         *
         * @return int|mixed
         */
        public function equipmentTaken($equipmentId)
        {

            /**
             * @var $LoanedEquipment LoanedEquipment
             */
            foreach($this->getLoanedEquipment() as $LoanedEquipment)
            {
                // if we found the equipment
                if($equipmentId == $LoanedEquipment->getEquipmentId())
                {
                    return true;
                }
            }

            return false;
        }



    }
}