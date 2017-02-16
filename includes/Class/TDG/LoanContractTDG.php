<?php
/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-02-16
 * Time: 4:26 PM
 */

namespace Stark\TDG
{
    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\TDG;

    class LoanContractTDG extends TDG
    {

        /**
         * @return mixed
         */
        public function getPk()
        {
            return "LoanContractId";
        }

        /**
         * @return string
         */
        public function getTable()
        {
            return "loan_contract";
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