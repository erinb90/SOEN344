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
         * @param \Stark\Models\Computer|\Stark\Interfaces\DomainObject $computer
         *
         * @return int
         * @throws \Exception
         */
        public function insert(DomainObject &$computer)
        {
            Registry::getConnection()->beginTransaction();
            $lastId = -1;

            try
            {
                Registry::getConnection()->insert($this->getParentTable(),
                    [
                        "Manufacturer" => $computer->getManufacturer(),
                        "ProductLine"  => $computer->getProductLine(),
                        "Description"  => $computer->getDescription(),
                        "Quantity"     => $computer->getQuantity()
                    ]
                );

                $lastId = Registry::getConnection()->lastInsertId();


                // computer specific
                Registry::getConnection()->insert($this->getTable(),
                    [
                        "Ram"         => $computer->getManufacturer(),
                        "Cpu"         => $computer->getCpu(),
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
         * @param \Stark\Models\Computer|\Stark\Interfaces\DomainObject $object
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
         * @param \Stark\Models\Computer|\Stark\Interfaces\DomainObject $object
         *
         * @return mixed
         */
        public function update(DomainObject &$object)
        {

            Registry::getConnection()->update(
                $this->getTable(),
                [
                    "Ram" => $object->getRam(),
                    "Cpu" => $object->getCpu()
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