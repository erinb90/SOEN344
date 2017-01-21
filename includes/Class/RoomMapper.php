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
	 * @param \stdClass $object
	 *
	 * @return mixed
	 */
	public function insert(stdClass &$object)
	{
		$this->_RoomTDG->insert($object);
	}

	/**
	 * @param \stdClass $object
	 *
	 * @return mixed
	 */
	public function delete(stdClass &$object)
	{
		$this->_RoomTDG->delete($object);
	}

	/**
	 * @param \stdClass $object
	 *
	 * @return mixed
	 */
	public function update(stdClass &$object)
	{
		$this->_RoomTDG->update($object);
	}

	public function findByPk($id)
	{
		return $this->getModel($this->_RoomTDG->findByPk($id));
	}
}
?>
