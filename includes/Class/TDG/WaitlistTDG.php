<?php

/**
 * Class WaitlistTDG
 */
class WaitlistTDG extends TDG
{
    /**
     * WaitlistTDG constructor.
     */
    public function __construct()
    {
    }


    /**
     * @param $uid int user id
     *
     * @return array returns all waitlist rows by for a particular student id
     */
    public function findAllUserWaitlists($uid)
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable());
        $query->where("studentID" . "=" . $uid);
        $sth = $query->execute();
        $m = $sth->fetchAll();
        return $m;
    }

    /**
     * @param \WaitlistDomain|DomainObject $waitlist
     *
     * @return string returns the last insert id made in the db
     */
    public function insert(DomainObject &$waitlist)
    {
        Registry::getConnection()->insert($this->getTable(),
            array(
                "studentID" => $waitlist->getStudentID(),
                "reservationID" => $waitlist->getReservationID()
            )

        );

        return Registry::getConnection()->lastInsertId();
    }

    /**
     * @param $reid int reservation
     *
     * @return array returns all waitlist rows by specified reservation
     */
    public function getWaitlistForReservation($reid)
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable());
        $query->where("reservationID" . "=" . $reid);
        $sth = $query->execute();
        $m = $sth->fetchAll();
        return $m;
    }

    /**
     * @param \WaitlistDomain|DomainObject $waitlist
     *
     * @return int the number of affected rows
     */
    public function update(DomainObject &$waitlist)
    {

        return Registry::getConnection()->update(
            $this->getTable(),
            array(
                "studentID" => $waitlist->getStudentID(),

                "reservationID" => $waitlist->getReservationID()
            ),
            array($this->getPk() => $waitlist->getWid())
        );
    }


    /**
     * @param \WaitlistDomain|DomainObject $waitlist wait list object
     *
     * @return int the number of affected rows
     */
    public function delete(DomainObject &$waitlist)
    {
        return Registry::getConnection()->delete($this->getTable(),
            array(
                $this->getPk() => $waitlist->getWid()
            )
        );
    }

    /**
     * @return string
     */
    public function getPk()
    {
        return "wid";
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return "waitlists";
    }
}