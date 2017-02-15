<?php
namespace Stark\Mappers;
use Stark\Interfaces\AbstractMapper;
use Stark\Models\ReservationDomain;
use Stark\TDG\ReservationTDG;
/**
 * Class ReservationMapper
 */
class ReservationMapper extends AbstractMapper
{

    /**
     * @var ReservationTDG
     */
	protected $tdg;

	/**
	 * ReservationMapper constructor.
	 */
	public function __construct()
	{
		$this->tdg = new ReservationTDG();
	}


    /**
     * @param $start
     * @param $studentid
     *
     * @return int
     */
	public function numberOfReservationsMadeWeekUser($start, $studentid)
	{
		$reservations = $this->getAllReservations();
		$weekDates = \Utilities::getWeek($start); // get week dates based on today's date
		$startDateWeek = $weekDates[0];
		$endDateWeek = $weekDates[1];
		$numberOfReservations = 0;
		/**
		 * @var $Reservation  ReservationDomain
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

    /**
     * @param $studentid
     *
     * @return array
     */
	public function findAllStudentReservations($studentid)
	{
		$data = $this->getTdg()->findAllStudentReservations($studentid);
		$models = [];
		foreach ($data as $row)
		{
			$models[] = $this->getModel($row);
		}
		return $models;
	}

    /**
     * @return array
     */
	public function getAllReservations()
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
	 * @param \array $data
	 *
	 * @return \Stark\Models\ReservationDomain
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
	 * @return ReservationTDG
	 */
	public function getTdg()
	{
		return $this->tdg;
	}
}
?>
