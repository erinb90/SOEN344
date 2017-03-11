<?php
namespace Stark\Interfaces
{

    use Doctrine\DBAL\Query\QueryBuilder;
    use Stark\Registry;

    /**
     * Abstract TDG class for all TDG classes to inherit from
     * Contains methods for making queries to DB
     *
     * @package Stark\Interfaces
     */
    abstract class TDG implements Gateway
    {

        /**
         * name of the table accessed by TDG
         * @var string
         */
        private $_table;

        /**
         * name of the primary key of table accessed by TDG
         * @var string
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
         * Insert new object into DB
         *
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return int returns the last inserted id
         */
        public abstract function insert(DomainObject &$object);

        /**
         * Delete object from DB
         *
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return mixed
         */
        public abstract function delete(DomainObject &$object);

        /**
         * Modify object in DB
         *
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return bool
         */
        public abstract function update(DomainObject &$object);

        /**
         * Find an entry in DB by its primary key
         *
         * @param $id
         *
         * @return array
         */
        public function findByPk($id)
        {
            return $this->query('*', [$this->getPk() => $id])[0];
        }

        /**
         * Return all entries for the given table
         *
         * @return array
         */
        public function findAll()
        {
            return $this->query('*');
        }

        /**
         * Makes an SQL query in the format:
         * SELECT (value of $select) WHERE (column value pairs in $where)
         * See Doctrine DBAL documentation for more details
         *
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