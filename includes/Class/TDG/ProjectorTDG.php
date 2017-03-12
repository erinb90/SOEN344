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
            Registry::getConnection()->beginTransaction();
            $lastId = -1;

            try
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


                Registry::getConnection()->commit();

            }
            catch (\Exception $e)
            {
                Registry::getConnection()->rollBack();

            }

            return $lastId;
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
            try
            {
                // This works under the assumption that CASCADE DELETE is on
                Registry::getConnection()->delete($this->getParentTable(),
                    [
                        $this->getParentPk() => $object->getEquipmentId()
                    ]
                );
                return true;
            }
            catch(\Exception $e)
            {

            }
            return false;

        }

        /**
         * Updates a Projector object in DB
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Projector $object
         *
         * @return bool
         */
        public function update(DomainObject &$object)
        {
            try
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

                return true;
            }
            catch(\Exception $e)
            {

            }
            return false;

        }
    }
}