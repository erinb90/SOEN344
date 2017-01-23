<?php

/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 11/3/2016
 * Time: 10:39 PM
 */
class WaitlistMapper extends AbstractMapper
{

    /**
     * @var \WaitlistTDG
     */
    private $tdg;

    /**
     * WaitlistMapper constructor.
     */
    public function __construct()
    {
        $this->tdg = new WaitlistTDG();
    }

    /**
     * @param array $data
     *
     * @return \WaitlistDomain
     */
    public function getModel($data)
    {
        if (empty($data) || !$data)
        {
            return null;
        }
        $Waitlist = new WaitlistDomain();
        $Waitlist->setReservationID($data['reservationID']);
        $Waitlist->setStudentID($data['studentID']);
        $Waitlist->setWid($data['wid']);

        return $Waitlist;
    }


    /**
     * @param $wid int waitlist id
     *
     * @return \WaitlistDomain waitlist object
     */
    public function findByPk($wid)
    {
        return $this->getModel($this->tdg->findByPk($wid));
    }

    /**
     * @param $sid int student id
     *
     * @return array an array of all waitlist for particular student
     */
    public function findAllUserWaitlists($sid)
    {
        $data = $this->tdg->findAllUserWaitlists($sid);
        $models = array();
        foreach ($data as $row)
        {
            $models[] = $this->getModel($row);
        }
        return $models;
    }

    /**
     * @param $reid int reservation id
     *
     * @return array an array of all wait list objects for a particular reservation
     */
    public function getWaitlistForReservation($reid)
    {
        $data = $this->tdg->getWaitlistForReservation($reid);
        $models = array();
        foreach ($data as $row)
        {
            $models[] = $this->getModel($row);
        }
        return $models;
    }

    /**
     * @return array an array of all waitlist objects found
     */
    public function getAllWaitlists()
    {
        $data = $this->tdg->findAll();
        $models = array();
        foreach ($data as $row)
        {
            $models[] = $this->getModel($row);
        }
        return $models;
    }

    /**
     * This method removes everyone that is waitlisted for a particular reservation
     * @param $reid int reservation id
     */
    public function removeWaitlistsForReservation($reid)
    {
        $waitlists = $this->getWaitlistForReservation($reid);
        foreach ($waitlists as $Waitlist)
        {
            $this->delete($Waitlist);
        }
    }

    /**
     * @param \WaitlistDomain|DomainObject $waitlist waitlist object
     *
     * @return string last insert id
     */
    public function insert(DomainObject &$waitlist)
    {
        return $this->tdg->insert($waitlist);
    }


    /**
     * @param \WaitlistDomain|DomainObject $waitlist waitlist object
     *
     * @return int number of affected rows
     */
    public function update(DomainObject &$waitlist)
    {
        return $this->tdg->update($waitlist);
    }

    /**
     * @param \WaitlistDomain|DomainObject $waitlist waitlist object
     *
     * @return int number of affected rows
     */
    public function delete(DomainObject &$waitlist)
    {
        return $this->tdg->delete($waitlist);
    }

    /**
     * @param $sid
     * @param $reid
     * @return WaitlistDomain
     */
    public function createWaitlistEntry($sid, $reid)
    {
        $WaitlistDomain = new WaitlistDomain();

        $WaitlistDomain->setStudentID($sid);
        $WaitlistDomain->setReservationID($reid);
        return $WaitlistDomain;
    }

    /**
     * @return WaitlistTDG
     */
    public function getTdg()
    {
        return $this->tdg;
    }
}