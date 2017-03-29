<?php

namespace Stark\TDG
{

    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Models\LockDomain;
    use Stark\Registry;

    /**
     * Class LockTDG
     * @package Stark\TDG
     */
    class LockTDG extends TDG
    {


        /**
         * LockTDG constructor.
         *
         * @param $table
         * @param $pk
         */
        public function __construct($table, $pk)
        {
            parent::__construct($table, $pk);
        }


        /**
         * @param $roomid
         *
         * @return mixed
         */
        public function getRoomLockByRoomId($roomid)
        {
            $query = Registry::getConnection()->createQueryBuilder();
            $query->select("*");
            $query->from($this->getTable(), $this->getTable());
            $query->where($this->getTable() . '.' . 'RoomId' . "='" . $roomid . "'");
            //$query->andWhere($this->getTable() . '.' . 'LockEndTime' . "<='" . date('Y-m-d H:i:s') . "'");
            $query->setMaxResults(1);
            $sth = $query->execute();
            $m = $sth->fetchAll();


            return $m[0];
        }


        /**
         * @param \Stark\Interfaces\DomainObject|LockDomain $object
         *
         * @return string
         */
        public function insert(DomainObject &$object)
        {
            Registry::getConnection()->insert($this->getTable(),
                [
                    "UserId" => $object->getUserId(),
                    "RoomId"    => $object->getRoomID(),
                    "LockStartTime"  => $object->getLockStartTime(),
                    "LockEndTime" => $object->getLockEndTime()
                ]
            );
        }

        /**
         * @param \Stark\Interfaces\DomainObject|LockDomain $object
         *
         * @return int
         */
        public function delete(DomainObject &$object)
        {
            Registry::getConnection()->delete($this->getTable(),
                [
                    $this->getPk() => $object->getLockId()
                ]
            );
        }

        /**
         * @param \Stark\Interfaces\DomainObject $object
         */
        public function update(DomainObject &$object)
        {
            // TODO: Implement update() method.
        }


    }
}