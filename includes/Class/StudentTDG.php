<?php
/**
 * Class StudentTDG
 */
class StudentTDG extends TDG
{


    public function findByEmail($username)
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable(), $this->getTable());
        $query->where($this->getTable() . '.' . "email" . "='" . $username . "'");
        $sth = $query->execute();
        $m = $sth->fetchAll();
        return $m[0];
    }


    /**
     * @return string
     */
    public function getPk()
    {
        return "studentID";
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return "students";
    }

    public function insert(DomainObject &$object)
    {
        // TODO: Implement insert() method.
    }

    public function delete(DomainObject &$object)
    {
        // TODO: Implement delete() method.
    }

    public function update(DomainObject &$object)
    {
        // TODO: Implement update() method.
    }


}
?>