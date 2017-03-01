<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/28/2017
 * Time: 11:05 PM
 */

namespace Stark
{


    use Stark\Mappers\ComputerMapper;
    use Stark\Mappers\ProjectorMapper;


    class EquipmentCatalog
    {

        private $_ProjectorMapper;
        private $_ComputerMapper;

        private $_equipment = [];

        public function __construct()
        {

            $this->_ComputerMapper = new ComputerMapper();
            $this->_ProjectorMapper = new ProjectorMapper();

            $this->mapEquipment();
        }


        private function mapEquipment()
        {

            $this->_equipment["computers"] = $this->_ComputerMapper->findAll();
            $this->_equipment["projectors"] = $this->_ProjectorMapper->findAll();





        }

        public function getAllEquipment()
        {

            return $this->_equipment;
        }

    }
}