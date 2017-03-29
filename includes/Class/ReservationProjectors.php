<?php

namespace Stark
{

    use Stark\Mappers\LoanContractMapper;
    use Stark\Mappers\LoanedEquipmentMapper;
    use Stark\Mappers\ProjectorMapper;
    use Stark\Mappers\ReservationMapper;

    use Stark\Models\LoanedEquipment;
    use Stark\Models\Projector;

    /**
     * Class ReservationProjectors
     * fetches a list/array of projectors for a particular reservation
     * @package Stark
     */
    class ReservationProjectors extends ReservationEquipment
    {

        private $_ProjectorMapper;

        public function __construct($reservationId)
        {
            parent::__construct($reservationId);
            $this->_ProjectorMapper = new ProjectorMapper();
        }

        public static function find($reservationId)
        {
            return new ReservationProjectors($reservationId);
        }

        /**
         * @return array
         */
        public function getProjectors()
        {


            $equipments = $this->_LoanedEquipmentMapper->findEquipmentByContractId($this->getLoanContract()->getLoanContractiD());



            $equipment = ["data" => []];
            /**
             * @var $LoanedEquipment  LoanedEquipment
             */
            foreach ($equipments as $LoanedEquipment)
            {

                /**
                 * @var $Projector Projector
                 */
                $Projector = $this->_ProjectorMapper->findByPk($LoanedEquipment->getEquipmentId());


                if($Projector)
                {
                    $equipment['data'][] = [

                        "EquipmentId" => $Projector->getEquipmentId(),
                        "Manufacturer" => $Projector->getManufacturer(),
                        "ProductLine" =>$Projector->getProductLine(),
                        "Description" => $Projector->getDescription(),
                        "Display" => $Projector->getDisplay(),
                        "Resolution" => $Projector->getResolution()
                    ];
                }

            }

            return $equipment;
        }


    }
}