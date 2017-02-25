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
     * @package Stark\TDG
     */
    class ProjectorTDG extends TDG
    {

        /**
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
                Registry::getConnection()->insert($this->getParentTable(),
                    [
                        "Manufacturer" => $object->getManufacturer(),
                        "ProductLine"  => $object->getProductLine(),
                        "Description"  => $object->getDescription(),
                        "Quantity"     => $object->getQuantity()
                    ]
                );

                $lastId = Registry::getConnection()->lastInsertId();


                // Projector specific
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
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Projector $object
         *
         * @return mixed
         */
        public function delete(DomainObject &$object)
        {
            // This works under the assumption that CASCADE DELETE is on
            return Registry::getConnection()->delete($this->getParentTable(),
                [
                    $this->getParentPk() => $object->getEquipmentId()
                ]
            );
        }

        /**
         * @param \Stark\Interfaces\DomainObject|\Stark\Models\Projector $object
         *
         * @return bool
         */
        public function update(DomainObject &$object)
        {
            Registry::getConnection()->update(
                $this->getTable(),
                [
                    "Display"    => $object->getDisplay(),
                    "Resolution" => $object->getResolution()
                ],
                [$this->getPk() => $object->getEquipmentId()]
            );


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