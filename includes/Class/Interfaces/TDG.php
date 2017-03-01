<?php
namespace Stark\Interfaces
{

    use Doctrine\DBAL\Query\QueryBuilder;
    use Stark\Registry;

    /**
     * Class TDG
     * @package Stark\Interfaces
     */
    abstract class TDG implements Gateway
    {

        /**
         * @var
         */
        private $_table;

        /**
         * @var
         */
        private $_pk;

        /**
         * @var null
         */
        private $_parentTable = NULL;

        /**
         * @var null
         */
        private $_parentPk = NULL;


        /**
         * TDG constructor.
         *
         * @param $table
         * @param $pk
         */
        public function __construct($table, $pk)
        {
            $this->_table = $table;
            $this->_pk = $pk;
        }

        /**
         * @param $table
         * @param $pk
         */
        public final function setParentTable($table, $pk)
        {
            $this->_parentPk = $pk;
            $this->_parentTable = $table;

        }

        /**
         * @return null
         */
        public function getParentPk()
        {
            return $this->_parentPk;
        }

        /**
         * @return null
         */
        public function getParentTable()
        {
            return $this->_parentTable;
        }


        /**
         * @return mixed
         */
        public function getPk()
        {
            return $this->_pk;
        }

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
         * @return int returns the last inserted id
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
         * @return bool
         */
        public abstract function update(DomainObject &$object);

        /**
         * @param $id
         *
         * @return array
         */
        public function findByPk($id)
        {
            return $this->query('*', [$this->getPk() => $id])[0];
        }

        /**
         * @return array
         */
        public function findAll()
        {
            return $this->query('*');
        }

        /**
         * @param $select
         * @param array $where [column => value]
         *
         * @return array
         */
        public function query($select, array $where = [])
        {

            /**
             * @var $parentQuery QueryBuilder
             */
            $parentQuery = Registry::getConnection()->createQueryBuilder();

            $parentQuery->select($select);
            $parentQuery->from($this->getTable());


            foreach ($where as $column => $value)
            {
                $parentQuery->where($this->getTable() . '.' . $column . '=' . "\"$value\"");
            }


            if ($this->getParentTable() != NULL)
            {
                $parentQuery->leftJoin($this->getTable(),
                    $this->getParentTable(),
                    $this->getParentTable(),
                    $this->getTable() . '.' . $this->getPk() . '=' . $this->getParentTable() . '.' . $this->getParentPk());
            }

            $sth = $parentQuery->execute();

            $m = $sth->fetchAll();


            return $m;
        }


    }


}