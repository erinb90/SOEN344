<?php
namespace Stark\Interfaces
{

    use Doctrine\DBAL\Query\QueryBuilder;
    use Stark\Registry;

    abstract class TDG implements Gateway
    {

        private $_table;

        private $_inheritance = [];

        public function __construct($table)
        {
            $this->_table = $table;
        }

        public final function setInheritance($table, $pk)
        {
            $this->_inheritance = [
                $table => $pk
            ];
            return $this;
        }

        /**
         * @return array
         */
        public final function getInheritance()
        {
            return $this->_inheritance;
        }

        /**
         * @return mixed
         */
        public abstract function getPk();

        /**
         * @return string
         */
        public final function getTable()
        {
            return $this->_table;
        }

        /**
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return int
         */
        public abstract function insert(DomainObject &$object);

        /**
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return mixed
         */
        public abstract function delete(DomainObject &$object);

        /**
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return mixed
         */
        public abstract function update(DomainObject &$object);


        private function joinInheritance(QueryBuilder &$query)
        {
            foreach($this->getInheritance() as $table => $pk)
            {
                $query->leftJoin($this->getTable(),
                    $table,
                    $table,
                    $this->getTable() .  $this->getPk() . '=' . $table  . $pk);
            }
        }

        /**
         * @param $id
         *
         * @return array
         */
        public function findByPk($id)
        {
            $query = Registry::getConnection()->createQueryBuilder();
            $query->select("*");
            $query->from($this->getTable(), $this->getTable());
            $this->joinInheritance($query);
            $query->where($this->getTable() . '.' . $this->getPk() . "='" . $id . "'");
            $sth = $query->execute();
            $m = $sth->fetchAll();
            return $m[0];
        }



        /**
         * @return array
         */
        public function findAll()
        {
            $query = Registry::getConnection()->createQueryBuilder();
            $query->select("*");
            $query->from($this->getTable());
            $this->joinInheritance($query);
            $sth = $query->execute();
            $m = $sth->fetchAll();
            return $m;
        }

    }
}