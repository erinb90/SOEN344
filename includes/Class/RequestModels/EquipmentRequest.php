<?php
namespace Stark\RequestModels;
/**
 * Class EquipmentRequest
 * @package Stark\RequestModels
 */
class EquipmentRequest
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
     * @var boolean $_allowAssignAlternative for the requested equipment.
     */
    private $_allowAssignAlternative;

    /**
     * EquipmentRequest constructor.
     *
     * @param int $equipmentId of the requested equipment
     * @param string $equipmentType of the requested equipment
     * @param boolean $allowAssignAlternative for the equipment if not available.
     */
    public function __construct($equipmentId, $equipmentType, $allowAssignAlternative)
    {
        $this->_equipmentId = $equipmentId;
        $this->_equipmentType = $equipmentType;
        $this->_allowAssignAlternative = $allowAssignAlternative;
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
     * @return string type of equipment to be requested
     */
    public function getEquipmentType()
    {
        return $this->_equipmentType;
    }

    /**
     * @param string $equipmentType to be requested
     */
    public function setEquipmentType($equipmentType)
    {
        $this->_equipmentType = $equipmentType;
    }

    /**
     * @return boolean if alternative equipment can be assigned.
     */
    public function allowAssignAlternative()
    {
        return $this->_equipmentType;
    }

    /**
     * @param boolean $allowAssignAlternative for the equipment request.
     */
    public function setAllowAssignAlternative($allowAssignAlternative)
    {
        $this->_allowAssignAlternative = $allowAssignAlternative;
    }
}