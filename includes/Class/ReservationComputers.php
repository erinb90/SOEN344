<?php

namespace Stark
{


    use Stark\Mappers\ComputerMapper;
    use Stark\Models\Computer;
    use Stark\Models\LoanedEquipment;

    class ReservationComputers extends ReservationEquipment
    {

        private $_ComputerMapper;

        /**
         * ReservationComputers constructor.
         *
         * @param $reservationId
         */
        public function __construct($reservationId)
        {
            parent::__construct($reservationId);
            $this->_ComputerMapper = new ComputerMapper();
        }

        /**
         * @param $reservationId
         *
         * @return mixed
         */
        public static function find($reservationId)
        {
            return new ReservationComputers($reservationId);
        }

        /**
         * @return array
         */
        public function getComputers()
        {


            $equipments = $this->_LoanedEquipmentMapper->findEquipmentByContractId($this->getLoanContract()->getLoanContractiD());

            $equipment = ["data" => []];
            /**
             * @var $LoanedEquipment  LoanedEquipment
             */
            foreach ($equipments as $LoanedEquipment)
            {

                /**
                 * @var $Computer Computer
                 */
                $Computer = $this->_ComputerMapper->findByPk($LoanedEquipment->getEquipmentId());


                if($Computer)
                {
                    $equipment['data'][] = [

                        "EquipmentId" => $Computer->getEquipmentId(),
                        "Manufacturer" => $Computer->getManufacturer(),
                        "ProductLine" =>$Computer->getProductLine(),
                        "Description" => $Computer->getDescription(),
                        "Cpu" => $Computer->getCpu(),
                        "Ram" => $Computer->getRam()
                    ];
                }

            }

            return $equipment;
        }
    }
}