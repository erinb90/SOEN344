<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 1:50 AM
 */

namespace Stark\Mappers
{


    use Stark\Interfaces\AbstractMapper;
    use Stark\Interfaces\DomainObject;
    use Stark\Models\Projector;
    use Stark\TDG\ProjectorTDG;

    /**
     * Mapper for Projector objects
     * Interacts with the ProjectorTDG to retrieve and manipulate Projector objects from DB
     *
     * @package Stark\Mappers
     */
    class ProjectorMapper extends AbstractMapper
    {

        /**
         * @var \Stark\TDG\ProjectorTDG
         */
        private $_tdg;

        /**
         * ProjectorMapper constructor.
         * Set the parent table to Equipment to mimic inheritance in the DB
         */
        public function __construct()
        {
            $this->_tdg = new ProjectorTDG("projectors", "EquipmentId");
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
         * Returns all Projector objects from DB
         * @return array
         */
        public function findAll()
        {
            $m = $this->getTdg()->findAll();
            $projectors = [];
            foreach ($m as $row)
            {
                $projectors[] = $this->getModel($row);
            }
            return $projectors;
        }

        /**
         * Creates a Projector object from a DB entry
         *
         * @param $data array data retrieve from the tdg

         * @return Projector returns a fully-dressed object
         */
        public function getModel(array $data = null)
        {
            if (!$data)
            {
                return NULL;
            }


            $Projector = new Projector();
            $Projector->setEquipmentId($data['EquipmentId']);
            $Projector->setDisplay($data['Display']);
            $Projector->setResolution($data['Resolution']);
            $Projector->setDescription($data['Description']);
            $Projector->setManufacturer($data['Manufacturer']);
            $Projector->setProductLine($data['ProductLine']);

            return $Projector;
        }

    }


}