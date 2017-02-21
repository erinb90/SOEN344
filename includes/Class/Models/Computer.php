<?php

namespace Stark\Models
{

    use Stark\Interfaces\DomainObject;

    /**
     * Class Computer
     * @package Stark\Models
     */
    class Computer extends Equipment implements DomainObject
    {

        /**
         * @var
         */
        private $_Ram;

        /**
         * @var
         */
        private $_Cpu;


        public function __construct()
        {
        }

        /**
         * @return mixed
         */
        public function getRam()
        {
            return $this->_Ram;
        }

        /**
         * @param mixed $Ram
         */
        public function setRam($Ram)
        {
            $this->_Ram = $Ram;
        }

        /**
         * @return mixed
         */
        public function getCpu()
        {
            return $this->_Cpu;
        }

        /**
         * @param mixed $Cpu
         */
        public function setCpu($Cpu)
        {
            $this->_Cpu = $Cpu;
        }




    }
}