<?php
/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-02-17
 * Time: 9:27 AM
 */

namespace Stark\TDG
{


    use Doctrine\DBAL\Query\QueryBuilder;
    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Registry;

    /**
     * Class ComputerTDG
     * Performs DB calls for Equipment and Computer tables
     * @package Stark\TDG
     */
    class ComputerTDG extends TDG
    {

        /**
         * ComputerTDG constructor.
         *
         * @param $table
         * @param $pk
         */
        public function __construct($table, $pk)
        {
            parent::__construct($table, $pk);
        }

        /**
         * Inserts a Computer object into DB
         *
         * @param \Stark\Models\Computer|\Stark\Interfaces\DomainObject $computer
         *
         * @return int
         * @throws \Exception
         */
        public function insert(DomainObject &$computer)
        {
            //Common equipment attributes to insert into parent Equipment table
            Registry::getConnection()->insert($this->getParentTable(),
                [
                    "Manufacturer" => $computer->getManufacturer(),
                    "ProductLine"  => $computer->getProductLine(),
                    "Description"  => $computer->getDescription()
                ]
            );

            //get the id of the last inserted row
            $lastId = Registry::getConnection()->lastInsertId();


            // Computer-specific attributes to insert into Computer table
            Registry::getConnection()->insert($this->getTable(),
                [
                    "Ram"         => $computer->getManufacturer(),
                    "Cpu"         => $computer->getCpu(),
                    "EquipmentId" => $lastId
                ]
            );
        }

        /**
         * Removes a Computer object from parent Equipment table
         * This should automatically remove the entry from the child Computer table
         *
         * @param \Stark\Models\Computer|\Stark\Interfaces\DomainObject $object
         *
         * @return bool
         */
        public function delete(DomainObject &$object)
        {
            // This works under the assumption that CASCADE DELETE is on
            Registry::getConnection()->delete($this->getParentTable(),
                [
                    $this->getParentPk() => $object->getEquipmentId()
                ]
            );
        }

        /**
         * Updates a Computer object in the DB
         *
         * @param \Stark\Models\Computer|\Stark\Interfaces\DomainObject $object
         *
         * @return bool
         */
        public function update(DomainObject &$object)
        {
            //update computer-specific attributes in Computer table
            Registry::getConnection()->update(
                $this->getTable(),
                [
                    "Ram" => $object->getRam(),
                    "Cpu" => $object->getCpu()
                ],
                [$this->getPk() => $object->getEquipmentId()]
            );

            //update common equipment attributes in Equipment table
            Registry::getConnection()->update(
                $this->getParentTable(),
                [
                    "Manufacturer" => $object->getManufacturer(),
                    "ProductLine"  => $object->getProductLine(),
                    "Description"  => $object->getDescription()
                ],
                [$this->getParentPk() => $object->getEquipmentId()]
            );
        }
    }
}