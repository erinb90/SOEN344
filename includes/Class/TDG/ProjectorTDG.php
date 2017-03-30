<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 1:50 AM
 */

namespace Stark\TDG
{

    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;
    use Stark\Registry;


    /**
     * Class ProjectorTDG
     * Performs DB calls for Projector and Equipment tables
     * @package Stark\TDG
     */
    class ProjectorTDG extends TDG
    {

        /**
         * Insert a Projector object into DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Projector $object
         *
         * @return int returns the last inserted id
         */
        public function insert(DomainObject &$object)
        {
            //Common equipment attributes to insert into parent Equipment table
            Registry::getConnection()->insert($this->getParentTable(),
                [
                    "Manufacturer" => $object->getManufacturer(),
                    "ProductLine"  => $object->getProductLine(),
                    "Description"  => $object->getDescription(),
                    "Quantity"     => $object->getQuantity()
                ]
            );

            $lastId = Registry::getConnection()->lastInsertId();

            // Projector-specific attributes to insert into Projector table
            Registry::getConnection()->insert($this->getTable(),
                [
                    "Display"     => $object->getManufacturer(),
                    "Resolution"  => $object->getResolution(),
                    "EquipmentId" => $lastId
                ]
            );
        }

        /**
         * Delete a Projector object from parent Equipment table
         * This should automatically remove the entry from the child Projector table
         *
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Projector $object
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
         * Updates a Projector object in DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Projector $object
         *
         * @return bool
         */
        public function update(DomainObject &$object)
        {
            //update projector-specific attributes in Projector table
            Registry::getConnection()->update(
                $this->getTable(),
                [
                    "Display"    => $object->getDisplay(),
                    "Resolution" => $object->getResolution()
                ],
                [$this->getPk() => $object->getEquipmentId()]
            );

            //update common equipment attributes in Equipment table
            Registry::getConnection()->update(
                $this->getParentTable(),
                [
                    "Manufacturer" => $object->getManufacturer(),
                    "ProductLine"  => $object->getProductLine(),
                    "Description"  => $object->getDescription(),
                    "Quantity"     => $object->getQuantity()
                ],
                [$this->getParentPk() => $object->getEquipmentId()]
            );
        }
    }
}