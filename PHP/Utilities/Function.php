<?php
session_start();
include_once "../Class/Unit.php";

function checkWeek($d, $current) {
	$_SESSION["confirmedRes"] = 0;
	//returns true if you are modifying a reservation, it is assumed existing reservations are within 3/week limit
	
	//Using slashes like we are, strtotime assumes mm/dd/yyyy, so fix
	//Reformate date and check for week in the year (of date being added)
	$reformatDate = date("j-m-Y", strtotime($d));

	// //Reformate date and check for week in the year (of date being added)

	$year = date("Y", strtotime($reformatDate));
	$week = date("W", strtotime($reformatDate));

	//Create counter, to be used to track if less than 3 reservations were made for that week 
	$counter = 0;

	//Check database table for all reservations under this student's ID
	// Compare the dates pulled with the week found when not looking at waitlist value
	for($x = 0; $x < count($current); $x++) {
		//Only if not on waitlist
		if($current[$x]["waitlisted"] == "0") {
			//Using slashes makes strtotime assume american date, aka m/d/y
			$tempDate = date("j-m-Y", strtotime($current[$x]["startTimeDate"]));
			$tempWeek = date("W", strtotime($tempDate));
		
			if($week == $tempWeek) {
				$counter++;
			}
		}
	}
	
	//return true if there aren't already 3 reservations made for that week
	$_SESSION["confirmedRes"] = $counter;

	if($_SESSION["modifying"])
	{
		return true;
	}

	if($counter < 3) {
		return true;
	}
	
	$_SESSION["userMSG"] = "You have already made 3 reservations this week";
	$_SESSION["msgClass"] = "failure";
	array_push($_SESSION["statusArray"], "weeklimited");
	
	return false;
}

function checkOverlap($start, $end, $current, $previousID) {

	$newStartTime = $start->format("Hi");
	$newEndTime = $end->format("Hi");
	//made global to get variable above
	for($x = 0; $x < count($current); $x++) {
		//Added IF check for modification, won't check overlap against itself
		//Added if check for soon to be deleted value
		if(($current[$x]->getID() != $_SESSION["reservationID"]))
		{
		
			//Get start and end time of new reservation
			$startTime = new DateTime($current[$x]->getStartTimeDate());
			$endTime = new DateTime($current[$x]->getEndTimeDate());

			//Format to Hi, allowing mathematical operators to be used on 24-hour clock
			$tempStart = $startTime->format("Hi");
			$tempEnd = $endTime->format("Hi");
			
			//If pulled value starts after the ending of the new reservation, ignore this case
			if($tempStart >= $newEndTime) {
				continue;
			}

			//If pulled value ends before the start of the new reservation, ignore this case
			else if($tempEnd <= $newStartTime) {
				continue;
			}
			//If it's not ignored, then this case is a conflict, return false
			else {
				if($_SESSION["sID"] == $current[$x]->getSID())
				{
					$_SESSION["selfReservation"] = true;
					return false;
				}
				$startFormat = $startTime->format("H:i");
				$endFormat = $endTime->format("H:i");

				//If there's an overlap, add new date to waitlist
				$_SESSION["userMSG"] = "This option overlaps, you've been added to the Waitlist!";
				$_SESSION["msgClass"] = "failure";
				return false;
			}
		}
	}
	
	return true;
}

function updateWaitlist($reserve, $roomID, $start, $conn) {
	$unit = new UnitOfWork($conn);
	
	$previousID = $reserve->getID();
	//Get all individuals on waitlist for room on this date
	$waitingReserves = $reserve->getReservationsByRoomAndDate($roomID, $start, 1, $conn);

	//For each reservation on waiting list for this section, checkWeek and Overlap, and add if passes
	$addedReservations = array();
	foreach($waitingReserves as $reservation) {
		//Values for checkWeek
		//Reformate start date for checkWeek
		$waitStartDate = substr($reservation->getStartTimeDate(),0,10);
		$dateElementsStart = explode("/", $waitStartDate);
		$dateEU = $dateElementsStart[2]."-".$dateElementsStart[1]."-".$dateElementsStart[0];
		
		//Get current reservations for this student
		$sID = $reservation->getSID();
	 	$currentReservations = $reserve->getReservations($sID, $conn);
	 	//All values for checkWeek present

		//Values for checkOverlap
		//Reformate before converting to DateTime
		$dateAmerStart = $dateElementsStart[1]."/".$dateElementsStart[2]."/".$dateElementsStart[0];
		$reformStart = $dateAmerStart.substr($reservation->getStartTimeDate(),10);

		//Reformate enddatetime
		$waitEndDate = substr($reservation->getEndTimeDate(),0,10);
		$dateElementsEnd = explode("/", $waitEndDate);
		$dateAmerEnd = $dateElementsEnd[1]."/".$dateElementsEnd[2]."/".$dateElementsEnd[0];
		$reformEnd = $dateAmerEnd.substr($reservation->getEndTimeDate(),10);

		//Convert to DateTime
		$startDateTime = new DateTime($reformStart);
		$endDateTime = new DateTime($reformEnd);

		//Get all reservations for the room that shares the start date 
		$availableTimes = $reserve->getReservationsByRoomAndDate($roomID, $start, 0, $conn);
	
		//All values for checkOverlap present

		//For each entry found, try to insert in order
		//If new value is insertable, add it
		$_SESSION["reservationID"] = $reservation->getID();
		
		if(checkWeek($dateEU, $currentReservations) && checkOverlap($startDateTime, $endDateTime, $availableTimes, $previousID)) {
			if(empty($addedReservations)) {
				$res = new ReservationMapper($reservation->getID(), $conn);

				$res->setStartTimeDate($reformStart);
				$res->setEndTimeDate($reformEnd);
				$res->setTitle($reservation->getTitle());
				$res->setDescription($reservation->getDescription());
				$res->setREID($reservation->getID());
				$res->setWait(0);

				array_push($addedReservations, $res);

				$unit->registerDirtyReservation($res);
			}
			//Check for overlap with values which will be "added", comparing to next item to be added.
			elseif(checkOverlap($startDateTime, $endDateTime, $addedReservations, $reservation->getID())) {
				$res = new ReservationMapper();

				$res->setStartTimeDate($reformStart);
				$res->setEndTimeDate($reformEnd);
				$res->setTitle($reservation->getTitle());
				$res->setDescription($reservation->getDescription());
				$res->setREID($reservation->getID());
				$res->setWait(0);

				array_push($addedReservations, $res);
				$unit->registerDirtyReservation($res);
			}
	 	}
	}
	$unit->commit();
}
?>