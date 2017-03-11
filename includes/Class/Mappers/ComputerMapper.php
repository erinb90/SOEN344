<?php
/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-02-17
 * Time: 9:26 AM
 */

namespace Stark\Mappers
{


    use phpDocumentor\Reflection\Types\Null_;
    use Stark\Interfaces\AbstractMapper;
    use Stark\Interfaces\DomainObject;
    use Stark\Models\Computer;
    use Stark\TDG\ComputerTDG;

    /**
     * Mapper for Computer objects
     * Interacts with the ComputerTDG to retrieve and manipulate Computer objects from DB
     *
     * @package Stark\Mappers
     */
    class ComputerMapper extends AbstractMapper
    {

        private $_tdg;

        /**
         * ComputerMapper constructor.
         * Set the parent table to Equipment to mimic inheritance in the DB
         */
        public function __construct()
        {
            $this->_tdg = new ComputerTDG("computers", "EquipmentId");

            $this->_tdg->setParentTable("equipment", "EquipmentId");
        }

        /**
         * @return \Stark\TDG\ComputerTDG
         */
        public function getTdg()
        {
            return $this->_tdg;
        }

        /**
         * Returns all Computer objects from DB
         * @return array
         */
        public function findAll()
        {
            $dbEntries = $this->getTdg()->findAll();

            $computers = [];
            foreach ($dbEntries as $row)
            {
                $computers[] = $this->getModel($row);
            }


            return $computers;
        }

        /**
         * Creates a Computer object from a DB entry
         *
         * @param $data array data retrieve from the tdg
         *
         * @return Computer returns a fully-dressed object
         */
        public function getModel(array $data = null)
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
            $Computer->setProductLine($data['ProductLine']);

            return $Computer;

        }

    }
}