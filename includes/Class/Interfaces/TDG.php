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
         * name of the parent table accessed by TDG in the case of inheritance
         * set to null by default
         * @var null
         */
        private $_parentTable = NULL;

        /**
         * name of the primary key of the parent table
         * set to null by default
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
         * set a parent table in the case of inheritance
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
         * Returns the ID of the last inserted row, or -1 if insert unsuccessful
         *
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return int returns the last inserted id
         */
        public abstract function insert(DomainObject &$object);

        /**
         * Delete object from DB
         * Returns true if delete was successful, false if not
         *
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return bool
         */
        public abstract function delete(DomainObject &$object);

        /**
         * Modify object in DB
         * Returns true if update was successful, false if not
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