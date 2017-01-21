<?php

/**
 * Class ReservationMapper
 */
class ReservationMapper extends AbstractMapper
{


	/**
	 * @var \TDG
	 */
	private $_ReservationTDG;

	public function __construct()
	{
		$this->_ReservationTDG = new ReservationTDG();
	}

    
	public function getReservations($sID, $conn){
		return $this->_ReservationTDG->getReservations($sID, $conn);
	}

	public function getReservationsByDate($start, $conn) {
		return $this->_ReservationTDG->getReservationsByDate($start, $conn);
	}

	public function getReservationsByRoomAndDate($rID, $start, $wait, $conn) {
		return $this->_ReservationTDG->getReservationsByRoomAndDate($rID, $start, $wait, $conn);
	}

	public function getWaitlistIDByStudent($sID, $reID, $conn) {
		return $this->_ReservationTDG->getWaitlistIDByStudent($sID, $reID, $conn);
	}

	public function getReservationsBySIDAndDate($sID, $start, $conn) {
		return $this->_ReservationTDG->getReservationsBySIDAndDate($sID, $start, $conn);
	}

	/*
		Unit of Work (TDG Functions for Room)
	*/	
	public function deleteReservation($reservationDeletedList, $conn)
	{
			$this->_ReservationTDG->deleteReservation($reservationDeletedList, $conn);

	}
	
	public function addReservation($reservationNewList, $conn)
	{
		$this->_ReservationTDG->addReservation($reservationNewList, $conn);
	}
	
	public function updateReservation($reservationUpdateList, $conn) {
		$this->_ReservationTDG->updateReservation($reservationUpdateList, $conn);
	}

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


		$ReservationDomain = new ReservationDomain();

		$ReservationDomain->setSID($data['studentID']);
		$ReservationDomain->setRID($data['roomID']);
		$ReservationDomain->setStartTimeDate($data['startTimeDate']);
		$ReservationDomain->setEndTimeDate($data['endTimeDate']);
		$ReservationDomain->setTitle($data['title']);
		$ReservationDomain->setDescription($data['description']);
		$ReservationDomain->setREID($data['reservationID']);

		return $ReservationDomain;
	}

	/**
	 * @param \stdClass $object
	 *
	 * @return mixed
	 */
	public function insert(stdClass &$object)
	{
		return $this->_ReservationTDG->insert($object);

	}

	/**
	 * @param \stdClass $object
	 *
	 * @return mixed
	 */
	public function delete(stdClass &$object)
	{
		return $this->_ReservationTDG->delete($object);

	}

	/**
	 * @param \stdClass $object
	 *
	 * @return mixed
	 */
	public function update(stdClass &$object)
	{
		return $this->_ReservationTDG->update($object);
	}

	public function findByPk($id)
	{
		return $this->getModel($this->_ReservationTDG->findByPk($id));
	}
}
?>
