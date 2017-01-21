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


	public function numberOfReservationsMadeWeekUser($start, $studentid)
	{

		//todo:
	}

	public function findAllStudentReservations($studentid)
	{
		//todo:
	}

	public function getAllReservations()
	{

		$data = $this->_ReservationTDG->findAll();
		$models = array();
		foreach ($data as $row)
		{
			$models[] = $this->getModel($row);
		}
		return $models;
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
	 * @param $studentid
	 * @param $roomid
	 * @param $starttime
	 * @param $endtime
	 * @param $title
	 * @param $description
	 * @return ReservationDomain
	 */
	public function createReservation($studentid, $roomid, $starttime, $endtime, $title, $description)
	{

		$ReservationDomain = new ReservationDomain();
		$ReservationDomain->setDescription($description);
		$ReservationDomain->setEndTimeDate($endtime);
		$ReservationDomain->setRID($roomid);
		$ReservationDomain->setSID($studentid);
		$ReservationDomain->setStartTimeDate($starttime);
		$ReservationDomain->setTitle($title);

		return $ReservationDomain;
	}

	/**
	 * @param \DomainObject $object
	 *
	 * @return mixed
	 */
	public function insert(DomainObject &$object)
	{
		return $this->_ReservationTDG->insert($object);

	}

	/**
	 * @param \DomainObject $object
	 *
	 * @return mixed
	 */
	public function delete(DomainObject &$object)
	{
		return $this->_ReservationTDG->delete($object);

	}

	/**
	 * @param \DomainObject $object
	 *
	 * @return mixed
	 */
	public function update(DomainObject &$object)
	{
		return $this->_ReservationTDG->update($object);
	}

	public function findByPk($id)
	{
		return $this->getModel($this->_ReservationTDG->findByPk($id));
	}
}
?>
