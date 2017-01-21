<?php

class RoomMapper extends AbstractMapper
{
	/**
	 * @var TDG $roomData
	 */
	private $_RoomTDG;


	public function __construct()
	{
		$this->_RoomTDG = new RoomTDG();
	}


	/**
	 * @return array an array of room objects
	 */
	public function getAllRooms()
	{
		$data = $this->_RoomTDG->findAll();
		$models = array();
		foreach ($data as $row)
		{
			$models[] = $this->getModel($row);
		}
		return $models;
	}

	/**
	public function checkBusy($rID, $conn){
        return $this->roomData->checkBusy($rID, $conn);
    }
	 * /
	 *

	 *
	/**
	 * @param \stdClass $data
	 *
	 * @return mixed
	 */
	public function getModel($data)
	{

		if(!$data)
		{
			return null;
		}

		$RoomDomain = new RoomDomain();


		$RoomDomain->setName($data['name']);
		$RoomDomain->setLocation($data['location']);
		$RoomDomain->setDescription($data['description']);
		$RoomDomain->setRID($data['roomID']);

		return $RoomDomain;


	}



	/**
	 * @param \DomainObject|RoomDomain $object
	 *
	 * @return mixed
	 */
	public function insert(DomainObject &$object)
	{
		$this->_RoomTDG->insert($object);
	}

	/**
	 * @param \DomainObject|RoomDomain $object
	 *
	 * @return mixed
	 */
	public function delete(DomainObject &$object)
	{
		$this->_RoomTDG->delete($object);
	}

	/**
	 * @param \DomainObject|RoomDomain $object
	 *
	 * @return mixed
	 */
	public function update(DomainObject &$object)
	{
		$this->_RoomTDG->update($object);
	}

	public function findByPk($id)
	{
		return $this->getModel($this->_RoomTDG->findByPk($id));
	}
}
?>
