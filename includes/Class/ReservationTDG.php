<?php

/**
 * Class ReservationTDG
 */
class ReservationTDG extends TDG
{


    /**
     * ReservationTDG constructor.
     */
	public function __construct()
    {

	}

    /**
     * @param $studentid
     *
     * @return array
     */
    public function findAllStudentReservations($studentid)
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable());
        $query->where("studentID" . "=" . $studentid);
        $sth = $query->execute();
        $m = $sth->fetchAll();
        return $m;
    }

    /**
     * @return string
     */
    public function getPk()
    {
        return "reservationID";
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return "reservations";
    }

    /**
     * @param \DomainObject|ReservationDomain $object
     *
     * @return int
     */
    public function insert(DomainObject &$object)
    {
        Registry::getConnection()->insert($this->getTable(),
            array(
                "studentID" => $object->getSID(),
                "roomID" => $object->getRID(),
                "startTimeDate" => $object->getStartTimeDate(),
                "endTimeDate" => $object->getEndTimeDate(),
                "title" => $object->getTitle(),
                "description" => $object->getDescription()
            )
        );
        return Registry::getConnection()->lastInsertId();
    }

    /**
     * @param \DomainObject|ReservationDomain $object
     *
     * @return int
     */
    public function delete(DomainObject &$object)
    {
        return Registry::getConnection()->delete($this->getTable(),
            array(
                $this->getPk() => $object->getReID()
            )
        );
    }


    /**
     * @param \DomainObject|ReservationDomain $object
     *
     * @return int
     */
    public function update(DomainObject &$object)
    {
        return Registry::getConnection()->update(
            $this->getTable(),
            array(
                "studentID" => $object->getSID(),
                "roomID" => $object->getRID(),
                "startTimeDate" => $object->getStartTimeDate(),
                "endTimeDate" => $object->getEndTimeDate(),
                "title" => $object->getTitle(),
                "description" => $object->getDescription()
            ),
            array($this->getPk(), $object->getReID())
        );
    }

}
?>