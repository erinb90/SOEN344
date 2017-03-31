<?php

namespace Stark\Models;


use Stark\Enums\EquipmentType;
use Stark\Interfaces\DomainObject;

class EquipmentRequest implements DomainObject
{
    /**
     * @var int id of the requested equipment.
     */
    private $_equipmentId;

    /**
     * @var string $_equipmentType of the requested equipment.
     */
    private $_equipmentType;


    /**
     * EquipmentRequest constructor.
     *
     * @param int $equipmentId of the requested equipment
     * @param string $equipmentType of the requested equipment
     */
    public function __construct($equipmentId, $equipmentType)
    {
        $this->_equipmentId = $equipmentId;
        $this->_equipmentType = $equipmentType;
    }

    /**
     * @return int id of the equipment to be requested
     */
    public function getEquipmentId()
    {
        return $this->_equipmentId;
    }

    /**
     * @param int $equipmentId the equipment to be requested
     *
     * @return void
     */
    public function setEquipmentId($equipmentId)
    {
        $this->_equipmentId = $equipmentId;
    }

    /**
     * @return string type of equipment to be requested (projector or computer)
     */
    public function getEquipmentType()
    {
        return $this->_equipmentType;
    }
}