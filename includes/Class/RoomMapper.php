<?php

/**
 * Class RoomMapper
 */
class RoomMapper extends AbstractMapper
{

	/**
	 * @var \RoomTDG
	 */
	protected $tdg;

	/**
	 * RoomMapper constructor.
	 */
	public function __construct()
	{
		$this->tdg = new RoomTDG();
	}

	/**
	 * @return \RoomTDG
	 */
	public function getTdg()
	{
		return $this->tdg;
	}

	/**
	 * @return array an array of RoomDomain objects
	 */
	public function getAllRooms()
	{
		$data = $this->getTdg()->findAll();
		$models = [];
		foreach ($data as $row)
		{
			$models[] = $this->getModel($row);
		}
		return $models;
	}

	/**
	 * @param \DomainObject|RoomDomain $data
	 *
	 * @return RoomDomain
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


}
?>
