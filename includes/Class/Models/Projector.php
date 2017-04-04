<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 1:45 AM
 */

namespace Stark\Models
{

    use Stark\Interfaces\Equipment;

    /**
     * Class Projector
     * @package Stark\Models
     */
    class Projector extends Equipment
    {


        /**
         * @var
         */
        private $_Display;

        /**
         * @var
         */
        private $_Resolution;


        /**
         * @return mixed
         */
        public function getDisplay()
        {
            return $this->_Display;
        }

        /**
         * @param mixed $Display
         */
        public function setDisplay($Display)
        {
            $this->_Display = $Display;
        }

        /**
         * @return mixed
         */
        public function getResolution()
        {
            return $this->_Resolution;
        }

        /**
         * @param mixed $Resolution
         */
        public function setResolution($Resolution)
        {
            $this->_Resolution = $Resolution;
        }


    }
}