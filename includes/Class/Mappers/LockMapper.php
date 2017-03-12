<?php

/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 1/22/2017
 * Time: 11:30 PM
 */

namespace Stark\Mappers
{

    use LockDomain;
    use Stark\Interfaces\AbstractMapper;
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

        protected $tdg;


        /**
         * LockMapper constructor.
         */
        public function __construct()
        {
            $this->tdg = new LockTDG();
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
            $LockDomain = new LockDomain();
            $LockDomain->setRoomID($data['roomID']);
            $LockDomain->setLocktime($data['Locktime']);
            $LockDomain->setStudentID($data['studentID']);
            $LockDomain->setLid($data['lid']);

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
         * Method to lock a room while a user is performing a write transaction
         * Creates a LockDomain object given a room ID and a User
         *
         * @param $roomid int this is the room id
         *
         * @return LockDomain
         */
        public function lockRoom($roomid, User $student)
        {
            $LockDomain = new LockDomain();
            $LockDomain->setLocktime(date("Y-m-d H:i:s"));
            $LockDomain->setStudentID($student->getStudentId());
            $LockDomain->setRoomID($roomid);

            return $LockDomain;

        }


        /**
         * Method to unlock a room once a user is finished with it
         * Retrieves the LockDomain entry from DB given its room ID, then instantiates a LockDomain object
         *
         * @param $roomid
         *
         * @return \LockDomain
         */
        public function unlockRoom($roomid)
        {
            $dbEntry = $this->getTdg()->getLockIdForRoom($roomid);

            $LockDomain = $this->getModel($dbEntry);

            return $LockDomain;
        }


    }
}