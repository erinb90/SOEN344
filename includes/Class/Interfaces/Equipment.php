<?php

namespace Stark\Interfaces
{

    /**
     * Abstract equipment class for equipment classes to inherit from
     * Contains attributes common to all equipment types along with getters and setters for each one.
     *
     * @package Stark\Interfaces
     */
    abstract class Equipment implements DomainObject
    {

        /**
         * @var int
         */
        private $_EquipmentId;

        /**
         * @var string
         */
        private $_Manufacturer;

        /**
         * @var string
         */
        private $_ProductLine;

        /**
         * @var string
         */
        private $_Description;


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
         * @return string
         */
        public function getManufacturer()
        {
            return $this->_Manufacturer;
        }

        /**
         * @param string $Manufacturer
         */
        public function setManufacturer($Manufacturer)
        {
            $this->_Manufacturer = $Manufacturer;
        }

        /**
         * @return string
         */
        public function getProductLine()
        {
            return $this->_ProductLine;
        }

        /**
         * @param string $ProductLine
         */
        public function setProductLine($ProductLine)
        {
            $this->_ProductLine = $ProductLine;
        }

        /**
         * @return string
         */
        public function getDescription()
        {
            return $this->_Description;
        }

        /**
         * @param string $Description
         */
        public function setDescription($Description)
        {
            $this->_Description = $Description;
        }

    }
}