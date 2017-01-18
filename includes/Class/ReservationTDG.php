<?php

// Start the session
session_start();

class ReservationTDG
{
    /* All times should be in the following format : STR_TO_DATE('10/24/11 10:00 PM','%m/%d/%Y %h:%i %p').
	 * 
	 * STR_TO_DATE('".$start."', '%m/%d/%Y %h:%i %p')
	 * STR_TO_DATE('".$end."', '%m/%d/%Y %h:%i %p')
	 * 
	*/
	
	/* The Insert method to add a new reservation into the reservation table
	*/
	
	//Replacement for the * in sql due to special formatting required for the DateTime columns
	private $star;
	
	public function __construct() {
		$this->star = "reservationID, studentID, roomID, date_format(startTimeDate,'%Y/%m/%d %H:%i') as startTimeDate, date_format(endTimeDate,'%Y/%m/%d %H:%i') as endTimeDate, title, description, waitlisted";
	}

	/* The Get methods for all Entities in the reservation table can be found here
     */
	public function getReservation($reID, $conn)
	{
		$sql = "SELECT {$this->star} FROM reservation WHERE reservationID ='".$reID."'";

		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$singleReservation = array("title" => $row["title"],
												"description" => $row["description"],
												"reservationID" => $row["reservationID"],
                                                "studentID" => $row["studentID"],
                                                "roomID" => $row["roomID"],
                                                "startTimeDate" => $row["startTimeDate"],
                                                "endTimeDate" => $row["endTimeDate"]);
		return $singleReservation;
	}	
	 
    public function getREID($sID, $conn){
		
		$sql = "SELECT reservationID FROM reservation WHERE studentID ='".$sID."'";
		$result = $conn->query($sql);
		
		$num = array();
		while($row = $result->fetch_assoc())
		{
			array_push($num, $row["reservationID"]);
		}
		
		return $num;
    }
    
    public function getStudentID($reID, $conn){
		
		$sql = "SELECT studentID FROM reservation WHERE reservationID ='".$reID."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["studentID"];
    }
      
    public function getRoomID($reID, $conn){
		
		$sql = "SELECT roomID FROM reservation WHERE reservationID ='".$reID."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["roomID"];
    }
	
	public function getStart($reID, $conn){
		
		$sql = "SELECT startTimeDate FROM reservation WHERE reservationID ='".$reID."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["startTimeDate"];
    }
	
	public function getEnd($reID, $conn){
		
		$sql = "SELECT endTimeDate FROM reservation WHERE reservationID ='".$reID."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["endTimeDate"];
    }
	
	public function getTitle($reID, $conn){

		$sql = "SELECT title FROM reservation WHERE reservationID ='".$reID."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		return $row["title"];
    }
	
	public function getDescription($reID, $conn){

		$sql = "SELECT description FROM reservation WHERE reservationID ='".$reID."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["description"];
    }
	
	//Get reservations by Student ID
	public function getReservations($sID, $conn) {
		
		$sql = "SELECT {$this->star} FROM reservation WHERE studentID ='".$sID."' ORDER BY startTimeDate ASC";
		$result = $conn->query($sql);

		$reservesDates = array();
        $singleReservation = array();
		
		while($row = $result->fetch_assoc())
		{   
                        $singleReservation = array("title" => $row["title"],
												"description" => $row["description"],
												"reservationID" => $row["reservationID"],
                                                "studentID" => $row["studentID"],
                                                "roomID" => $row["roomID"],
                                                "startTimeDate" => $row["startTimeDate"],
                                                "endTimeDate" => $row["endTimeDate"],
												"waitlisted" => $row["waitlisted"]);
			array_push($reservesDates, $singleReservation);
		}
		
		return $reservesDates;
	}
    
	//Get reservations by Date ONLY
	public function getReservationsByDate($start, $conn) {
		
		$date = substr($start,0,10);

		//Need to reformate date so that it can be used in the database
		$dateElements = explode("/", $date);
		$reformatDate = $dateElements[2]."-".$dateElements[0]."-".$dateElements[1];

		$sql = "SELECT {$this->star} FROM reservation WHERE date(startTimeDate) = str_to_date('".$reformatDate."','%Y-%d-%m')"
				. " and waitlisted = false";

		$result = $conn->query($sql);

		$reservesTimes = array();
		if(!is_null($reservesTimes)) {
			while($row = $result->fetch_assoc())
			{
				$temp = new ReservationDomain($row["reservationID"], $row["studentID"], $row["roomID"], $row["startTimeDate"], $row["endTimeDate"], $row["title"], $row["description"]);
				array_push($reservesTimes, $temp);
			}
		}
		return $reservesTimes; 
	}
	
	//Get reservations by room ID AND Date (for overlap checking)
	public function getReservationsByRoomAndDate($roomID, $start, $wait, $conn) {

		$date = substr($start,0,10);

		//Need to reformate date so that it can be used in the database
		$dateElements = explode("/", $date);
		$reformatDate = $dateElements[2]."-".$dateElements[0]."-".$dateElements[1];

		$sql = "SELECT ".$this->star." FROM reservation WHERE date(startTimeDate) = date '".$reformatDate."'"
				. " AND roomID = '".$roomID."' and waitlisted = '".$wait."' ORDER BY reservationID ASC";
		
		$result = $conn->query($sql);
		
		$reservesTimes = array();

		if($result != null) {
			while($row = $result->fetch_assoc())
			{
				$temp = new ReservationDomain($row["reservationID"], $row["studentID"], $row["roomID"], $row["startTimeDate"], $row["endTimeDate"], $row["title"], $row["description"], $row["waitlisted"]);
				array_push($reservesTimes, $temp);
			}
		}

		return $reservesTimes; 
	}

	//Returns waitlisted options
	public function getReservationsBySIDAndDate($sID, $start, $conn) {
		$reserves = array();

		$date = substr($start,0,10);

		//Need to reformate date so that it can be used in the database
		$dateElements = explode("/", $date);
		$reformatDate = $dateElements[2]."-".$dateElements[0]."-".$dateElements[1];

		//First, get all elements which share a startDate and are on the waitlist from the student
		$sql1 = "SELECT * FROM reservation WHERE date(startTimeDate) = date '".$reformatDate."'"
		. "AND studentID = '".$sID."' AND waitlisted = true"; 

		$result = $conn->query($sql1);

		if($result != null) {
			while($row = $result->fetch_assoc())
			{
				$temp = new ReservationDomain($row["reservationID"], $row["studentID"], $row["roomID"], $row["startTimeDate"], $row["endTimeDate"], $row["title"], $row["description"], $row["waitlisted"]);
				array_push($reserves, $temp);
			}
		}

		return $reserves;
	}

	public function getWaitlistIDByStudent($sID, $reID, $conn) {
		$sql = "SELECT * FROM reservation WHERE studentID ='".$sID."' AND reservationID != '".$reID."' AND waitlisted = true";
		$result = $conn->query($sql);

		$waitListDates = array();
		
		while($row = $result->fetch_assoc())
		{   
			$temp = new ReservationDomain($row["reservationID"], $row["studentID"], $row["roomID"], $row["startTimeDate"], $row["endTimeDate"], $row["title"], $row["description"], $row["waitlisted"]);
			array_push($waitListDates, $temp);
		}
		
		return $waitListDates;
	}
	
	/* 
		The Insert method to add a new reservation into the reservation table
	*/
	public function addReservation($reservationNewList, $conn)
	{
		foreach($reservationNewList as &$reservationNew)
		{
			$startTrans = "STR_TO_DATE('".$reservationNew->getStartTimeDate()."', '%m/%d/%Y %H:%i')";
			$endTrans = "STR_TO_DATE('".$reservationNew->getEndTimeDate()."', '%m/%d/%Y %H:%i')";

			$sID = $reservationNew->getSID();
			$rID = $reservationNew->getRID();
			$title = $reservationNew->getTitle();
			$desc = $reservationNew->getDescription();
			$wait = $reservationNew->getWait();
			
			$sql = "INSERT INTO reservation (studentID, roomID, startTimeDate, endTimeDate, title, description, waitlisted) 
				Values ('".$sID."','".$rID."',".$startTrans.",".$endTrans.",'".$title."','".$desc."','".$wait."')";
			$result = $conn->query($sql);
		}
	}
	
	public function updateReservation($reservationUpdateList, $conn){
	
		foreach($reservationUpdateList as &$reservationUpdate)
		{
			$startTrans = "STR_TO_DATE('".$reservationUpdate->getStartTimeDate()."', '%m/%d/%Y %H:%i')";
			$endTrans = "STR_TO_DATE('".$reservationUpdate->getEndTimeDate()."', '%m/%d/%Y %H:%i')";

			$reID = $reservationUpdate->getID();
			$title = $reservationUpdate->getTitle();
			$desc = $reservationUpdate->getDescription();
			$wait = $reservationUpdate->getWait();
			
			$sql = "Update reservation set startTimeDate = ".$startTrans.", endTimeDate = ".$endTrans.",
				title = '".$title."', description = '".$desc."', waitlisted = '".$wait."'
				WHERE reservationID ='".$reID."'";

			$result = $conn->query($sql);
		}
	}
	
	public function deleteReservation($reservationDeletedList, $conn){

		foreach($reservationDeletedList as &$reservationDeleted)
		{
			$sql = "DELETE FROM reservation WHERE reservationID ='".$reservationDeleted->getID()."'";
			$result = $conn->query($sql);
		}
	}
}
?>