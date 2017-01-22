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
		$reservations = $this->getAllReservations();
		$weekDates = Utilities::getWeek($start); // get week dates based on today's date
		$startDateWeek = $weekDates[0];
		$endDateWeek = $weekDates[1];
		$numberOfReservations = 0;
		/**
		 * @var $Reservation ReservationDomain
		 */
		foreach ($reservations as $Reservation)
		{
			// find this user's reservations
			if ($Reservation->getSID() == $studentid)
			{
				if (strtotime($Reservation->getStartTimeDate()) >= strtotime($startDateWeek) && strtotime($Reservation->getEndTimeDate()) <= strtotime($endDateWeek))
				{
					$numberOfReservations++;
				}
			}
		}
		return $numberOfReservations;
	}

	public function findAllStudentReservations($studentid)
	{
		$data = $this->_ReservationTDG->findAllStudentReservations($studentid);
		$models = array();
		foreach ($data as $row)
		{
			$models[] = $this->getModel($row);
		}
		return $models;
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
    


	/**
	 * @param \array $data
	 *
	 * @return ReservationDomain
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
