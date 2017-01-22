<?php

class ReservationTDG extends TDG
{


	public function __construct()
    {

	}

	public function findAll()
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable());
        $sth = $query->execute();
        $m = $sth->fetchAll();
        return $m;
    }

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

    public function getPk()
    {
        return "reservationID";
    }

    public function getTable()
    {
        return "reservations";
    }

    /**
     * @param \DomainObject|ReservationDomain $object
     *
     * @return string
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
                $this->getPk() => $object->getReid()
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

    /**
     * @param $id
     *
     * @return mixed
     */
    public function findByPk($id)
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable(), $this->getTable());
        $query->where($this->getTable() . '.' . $this->getPk() . "='" . $id . "'");
        $sth = $query->execute();
        $m = $sth->fetchAll();
        return $m[0];
    }
}
?>