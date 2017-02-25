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

    /**
     * Class ComputerMapper
     * @package Stark\Mappers
     */
    class ComputerMapper extends AbstractMapper
    {

        private $_tdg;

        /**
         * ComputerMapper constructor.
         */
        public function __construct()
        {
            $this->_tdg = new ComputerTDG("computers", "EquipmentId");

            $this->_tdg->setParentTable("equipment", "EquipmentId");
        }

        /**
         * @return \Stark\Interfaces\TDG
         */
        public function getTdg()
        {
            return $this->_tdg;
        }

        /**
         * @return array
         */
        public function findAll()
        {
            $m = $this->getTdg()->findAll();

            $computers = [];
            foreach ($m as $row)
            {
                $computers[] = $this->getModel($row);
            }


            return $computers;
        }

        /**
         * @param $data array data retrieve from the tdg
         *
         * @return DomainObject returns a fully-dressed object
         */
        public function getModel(array $data)
        {
            if (!$data)
            {
                return NULL;
            }


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