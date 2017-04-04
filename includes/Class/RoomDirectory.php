<?php
namespace Stark
{

    use Stark\Mappers\RoomMapper;

    /**
     * Class RoomDirectory
     * @package Stark
     */
    class RoomDirectory
    {

        /**
         * @var RoomMapper
         */
        private $_RoomMapper;

        /**
         * RoomDirectory constructor.
         */
        public function __construct()
        {
            $this->_RoomMapper = new RoomMapper();
        }

        /**
         * @return array returns a list of RoomDomain objects
         */
        public function getRooms()
        {
            return $this->_RoomMapper->findAll();

        }
    }
}