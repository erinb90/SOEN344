<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 1:18 AM
 */

namespace Stark\Mappers
{


    use Stark\Interfaces\AbstractMapper;
    use Stark\Interfaces\DomainObject;
    use Stark\Models\Room;
    use Stark\TDG\RoomTDG;

    class RoomMapper extends AbstractMapper
    {

        private $_tdg;

        /**
         * RoomMapper constructor.
         */
        public function __construct()
        {
            $this->_tdg = new RoomTDG("rooms", "RoomId");
        }

        /**
         * @return \Stark\Interfaces\TDG
         */
        public function getTdg()
        {
            return $this->_tdg;
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

            $Room = new Room();
            $Room->setLocation($data['Location']);
            $Room->setName($data['Name']);
            $Room->setRoomID($data['RoomId']);

            return $Room;
        }
    }
}