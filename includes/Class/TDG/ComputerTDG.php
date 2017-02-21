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

    class ComputerTDG extends TDG
    {

        public function __construct($table)
        {
            parent::__construct($table);
        }

        /**
         * @return mixed
         */
        public function getPk()
        {
            return "EquipmentId";
        }






        /**
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return int
         */
        public function insert(DomainObject &$object)
        {
            // TODO: Implement insert() method.
        }

        /**
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return mixed
         */
        public function delete(DomainObject &$object)
        {
            // TODO: Implement delete() method.
        }

        /**
         * @param \Stark\Interfaces\DomainObject $object
         *
         * @return mixed
         */
        public function update(DomainObject &$object)
        {
            // TODO: Implement update() method.
        }
    }
}