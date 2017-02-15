<?php

/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 1/22/2017
 * Time: 11:30 PM
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
     * @param $data
     *
     * @return LockDomain
     */
    public function getModel($data)
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
     * @param $roomid int this is the room id
     * @return LockDomain
     */
    public function lockRoom($roomid, StudentDomain $student)
    {
        $LockDomain = new LockDomain();
        $LockDomain->setLocktime(date("Y-m-d H:i:s"));
        $LockDomain->setStudentID($student->getSID());
        $LockDomain->setRoomID($roomid);

        return $LockDomain;

    }


    /**
     * @param $id
     *
     * @return \LockDomain
     */
    public function unlockRoom($id)
    {
        $data = $this->getTdg()->getLockIdForRoom($id);

        $LockDomain = $this->getModel($data);

        return $LockDomain;
    }


}