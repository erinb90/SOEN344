<?php


namespace Stark\Mappers
{


    use Stark\Interfaces\AbstractMapper;

    use Stark\Models\LockDomain;
    use Stark\Models\User;
    use Stark\TDG\LockTDG;

    /**
     * Mapper for LockDomain objects
     * Interacts with the LockTDG to retrieve and manipulate LockDomain objects from DB
     *
     * Class LockMapper
     * @package Stark\Mappers
     */
    class LockMapper extends AbstractMapper
    {

        /**
         * @var \Stark\TDG\LockTDG
         */
        protected $tdg;


        /**
         * LockMapper constructor.
         */
        public function __construct()
        {
            $this->tdg = new LockTDG("locks", "lockId");
        }

        /**
         * Creates a LockDomain object from a DB entry
         *
         * @param $data array data retrieve from the tdg
         *
         * @return LockDomain
         */
        public function getModel(array $data = null)
        {
            if(!$data)
                return null;
            $LockDomain = new LockDomain();
            $LockDomain->setRoomId($data['roomID']);
            $LockDomain->setLockStartTime($data['LockStartTime']);
            $LockDomain->setLockEndTime($data['LockEndTime']);
            $LockDomain->setLockId($data['lockId']);
            $LockDomain->setUserId($data['UserId']);

            return $LockDomain;
        }


        /**
         * @return LockTDG
         */
        public function getTdg()
        {
            return $this->tdg;
        }

        /**
         * @param $roomid
         * @param $startTime
         * @param $endTime
         * @param \Stark\Models\User $student
         *
         * @return \Stark\Models\LockDomain
         */
        public function lockRoom($roomid, $startTime, $endTime,  User $student)
        {
            $LockDomain = new LockDomain();
            $LockDomain->setLockStartTime($startTime);
            $LockDomain->setLockEndTime($endTime);
            $LockDomain->setRoomID($roomid);
            $LockDomain->setUserId($student->getUserId());

            return $LockDomain;

        }

        /**
         * @param $roomid
         *
         * @return bool
         */
        public function isLocked($roomid)
        {
            return !$this->getRoomLockByRoomId($roomid);
        }

        /**
         * @param $roomid
         *
         * @return \Stark\Models\LockDomain
         */
        public function getRoomLockByRoomId($roomid)
        {
            $lock = $this->getModel( $this->getTdg()->getRoomLockByRoomId($roomid) );
            return $lock;
        }



    }
}