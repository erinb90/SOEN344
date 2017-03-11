<?php

namespace Stark\Interfaces
{

    /**
     * Class Equipment
     * @package Stark\Interfaces
     */
    abstract class Equipment implements DomainObject
    {

        /**
         * @var int
         */
        private $_EquipmentId;

        /**
         * @var
         */
        private $_Manufacturer;

        /**
         * @var
         */
        private $_ProductLine;

        /**
         * @var
         */
        private $_Description;

        /**
         * @var
         */
        private $_Discriminator;
        
        /**
         * Equipment constructor.
         */
        public function __construct()
        {
        }

        /**
         * @return int
         */
        public final function getEquipmentId()
        {
            return $this->_EquipmentId;
        }

        /**
         * @param int $EquipmentId
         */
        public function setEquipmentId($EquipmentId)
        {
            $this->_EquipmentId = $EquipmentId;
        }

        /**
         * @return mixed
         */
        public function getManufacturer()
        {
            return $this->_Manufacturer;
        }

        /**
         * @param mixed $Manufacturer
         */
        public function setManufacturer($Manufacturer)
        {
            $this->_Manufacturer = $Manufacturer;
        }

        /**
         * @return mixed
         */
        public function getProductLine()
        {
            return $this->_ProductLine;
        }

        /**
         * @param mixed $ProductLine
         */
        public function setProductLine($ProductLine)
        {
            $this->_ProductLine = $ProductLine;
        }

        /**
         * @return mixed
         */
        public function getDescription()
        {
            return $this->_Description;
        }

        /**
         * @param mixed $Description
         */
        public function setDescription($Description)
        {
            $this->_Description = $Description;
        }

        /**
         * @return mixed
         */
        public function getDiscriminator()
        {
            return $this->_Discriminator;
        }

        /**
         * @param mixed $Discriminator
         */
        public function setDiscriminator($Discriminator)
        {
            $this->_Discriminator = $Discriminator;
        }
    }
}