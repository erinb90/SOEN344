<?php
include "../Class/StudentMapper.php";
include "../Class/RoomMapper.php";
include "../Class/ReservationMapper.php";
include_once dirname(__FILE__).'/../Utilities/ServerConnection.php';

// Start the session
session_start();

$conn = getServerConn();

$wrongTime = "Your End Time must be after your Start Time! Please try again.";
$tooLong = "You cannot reserve for a time of more than 3 hours!";

$title = htmlspecialchars($_POST["title"]);
$desc = htmlspecialchars($_POST["description"]);

$passedDate = htmlspecialchars($_POST["dateDrop"]);
$start = htmlspecialchars($_POST["startTime"]);
$end = htmlspecialchars($_POST["endTime"]);

$first = htmlspecialchars($_POST["firstName"]);
$last = htmlspecialchars($_POST["lastName"]);
$sID = htmlspecialchars($_POST["studentID"]);
$prog = htmlspecialchars($_POST["program"]);
$email = htmlspecialchars($_POST["email"]);

//Getting the ID of the Room 1
//Should Obtain Either 1,2,3,4,5
$rID = htmlspecialchars($_POST["roomNum"]);

$student = new StudentMapper($email, $conn);
$room = new RoomMapper($rID, $conn);
$reservation = new ReservationMapper();

$name = $room->getName();

$startEx = explode(":", $start);
$startFloat = ($startEx[0] + ($startEx[1]/60));

$endEx = explode(":", $end);
$endFloat = ($endEx[0] + ($endEx[1]/60));

/*
*	If reservation will last more than 3 hours
*/
if ( ($endFloat-$startFloat) > 3)
{
	$_SESSION["userMSG"] = $tooLong;
	$_SESSION["msgClass"] = "failure";
}

else if ($endFloat <= $startFloat)
{
	$_SESSION["userMSG"] = $wrongTime;
	$_SESSION["msgClass"] = "failure";
}	
else
{
	//Converting the Date to the Proper Format
	//Should Obtain DD/MM/YYYY
	$dateEU = date('d-m-Y', strtotime($passedDate));
	$dateAmer = date('m/d/Y', strtotime($passedDate));
	$start = $dateAmer." ".$start;
	$end = $dateAmer." ".$end;

	//Check for presence of more than 3 reservations in the same week 
	//before actually adding the reservation
	$currentReservations = $reservation->getReservations($sID, $conn);


	//Get the list of reservations in same room and on same day
	$availableTimes = $reservation->getReservationsByRoomAndDate($rID, $start, $conn);

	//Get start and end time of new reservation, convert the difference to mins to find duration
	$startDate = new DateTime($start);
	$endDate = new DateTime($end);

	//Total duration of new reservation
//	$total = getDuration($startDate, $endDate);

	if(checkWeek($dateEU, $sID, $currentReservations) && checkOverlap($startDate, $endDate, $availableTimes)) {
		//Just realize display message is in format mm/dd/yyyy
		$reservation->addReservation($sID, $rID, $start, $end, $title, $desc, $conn);
		$_SESSION["userMSG"] = "You have successfully made a reservation for ".$start." to ".$end. " in Room ".$name."!";
		$_SESSION["msgClass"] = "success";
	}
}

closeServerConn($conn);

header("Location: Home.php");


function checkWeek($d, $s, $current) {
	//Using slashes like we are, strtotime assumes mm/dd/yyyy, so fix
	//Reformate date and check for week in the year (of date being added)
	$reformatDate = date("j-m-Y", strtotime($d));

	// //Reformate date and check for week in the year (of date being added)

	$year = date("Y", strtotime($reformatDate));
	$week = date("W", strtotime($reformatDate));

	//Create counter, to be used to track if less than 3 reservations were made for that week 
	$counter = 0;

	//Check database table for all reservations under this student's ID
	// Compare the dates pulled with the week found
	for($x = 0; $x < count($current); $x++) {

		//Using slashes makes strtotime assume american date, aka m/d/y
		$tempDate = date("j-m-Y", strtotime($current[$x]["startTimeDate"]));
		$tempWeek = date("W", strtotime($tempDate));

		//    echo "Current week: " . $week . " Pulled week: " . $tempWeek;
		//    echo "<br>";

		if($week == $tempWeek) {
			$counter++;
		}
	}
	
	//return true if there aren't already 3 reservations made for that week
	if($counter < 3) {
		return true;
	}

	$_SESSION["userMSG"] = "You have already made 3 reservations this week";
	$_SESSION["msgClass"] = "failure";
	return false;
}

function checkOverlap($start, $end, $current) {
	$newStartTime = $start->format("Hi");
	$newEndTime = $end->format("Hi");

	for($x = 0; $x < count($current); $x++) {
		//Get start and end time of new reservation, convert the difference to mins to find duration
		$startTime = new DateTime($current[$x]->getStartTimeDate());
		$endTime = new DateTime($current[$x]->getEndTimeDate());

		$tempStart = $startTime->format("Hi");
		$tempEnd = $endTime->format("Hi");
		
	//	echo "Start: " . $tempStart . " End: " . $tempEnd."<br>";
		
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
			$startFormat = $startTime->format("H:i");
			$endFormat = $endTime->format("H:i");

			$_SESSION["userMSG"] = "This option overlaps with the reservation beginning at " .$startFormat. " and ending at ".$endFormat;
	  		$_SESSION["msgClass"] = "failure";
			return false;
		}
	}
	return true;
}

//Get duration of reservation, from start to end, in mins
//LEAVE FOR NOW, MIGHT NEED DURATION IN FUTURE

// function getDuration($startTime, $endTime) {
// 	$diff = date_diff($startTime, $endTime);
// 	$dateArray = explode(":", $diff->format('%h:%i'));
// 	$totalMinutes = $dateArray[0]*60 + $dateArray[1];
// 	return $totalMinutes;
// }
?>