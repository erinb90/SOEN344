<?php

namespace Stark\TDG
{

    use Stark\Interfaces\DomainObject;
    use Stark\Registry;

    /**
     * Class LockTDG
     * @package Stark\TDG
     */
    class LockTDG extends TDG
    {

        /**
         * @return mixed
         */
        public function getPk()
        {
            return "lid";
        }

        /**
         * @return mixed
         */
        public function getTable()
        {
            return "roomlocks";
        }


        public function getLockIdForRoom($roomid)
        {
            $query = Registry::getConnection()->createQueryBuilder();
            $query->select("*");
            $query->from($this->getTable(), $this->getTable());
            $query->where($this->getTable() . '.' . 'roomID' . "='" . $roomid . "'");
            $query->orderBy($this->getPk(), "DESC");

            $query->setMaxResults(1);

            $sth = $query->execute();
            $m = $sth->fetchAll();

            return $m[0];
        }

        /**
         * @param \DomainObject|LockDomain $object
         *
         * @return int
         */
        public function insert(DomainObject &$object)
        {
            Registry::getConnection()->insert($this->getTable(),
                array(
                    "studentID" => $object->getStudentID(),
                    "roomID"    => $object->getRoomID(),
                    "Locktime"  => date("Y-m-d H:i:s")
                )
            );

            return Registry::getConnection()->lastInsertId();
        }

        /**
         * @param \DomainObject|LockDomain $object
         *
         * @return mixed
         */
        public function delete(DomainObject &$object)
        {
            return Registry::getConnection()->delete($this->getTable(),
                array(
                    $this->getPk() => $object->getLid()
                )
            );
        }

        /**
         * @param \DomainObject $object
         *
         * @return mixed
         */
        public function update(DomainObject &$object)
        {
            // TODO: Implement update() method.
        }


    }
}