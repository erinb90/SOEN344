<?php
/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-02-17
 * Time: 9:26 AM
 */

namespace Stark\Mappers
{


    use Stark\Interfaces\AbstractMapper;
    use Stark\Interfaces\DomainObject;
    use Stark\Models\Computer;
    use Stark\TDG\ComputerTDG;

    class ComputerMapper extends AbstractMapper
    {

        private $_tdg;
        /**
         * @return \Stark\Interfaces\TDG
         */
        public function getTdg()
        {
            $this->_tdg = new ComputerTDG("computers");
        }

        /**
         * @param $data array data retrieve from the tdg
         *
         * @return DomainObject returns a fully-dressed object
         */
        public function getModel(array $data)
        {
            if(!$data)
                return null;

            $Computer = new Computer();
            $Computer->setEquipmentId($data['EquipmentId']);
            $Computer->setRam($data['Ram']);
            $Computer->setCpu($data['Cpu']);
            $Computer->setDescription($data['Description']);
            $Computer->setManufacturer($data['Manufacturer']);
            $Computer->setQuantity($data['Quantity']);
            $Computer->setProductLine($data['ProductLine']);

            return $Computer;

        }

    }
}