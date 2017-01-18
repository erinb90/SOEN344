<?php
include "../Class/RoomMapper.php";
include "../Class/StudentMapper.php";
include "../Class/ReservationMapper.php";

include_once "../Class/Unit.php";
include_once dirname(__FILE__).'/../Utilities/ServerConnection.php';
include '../Utilities/Function.php';
// Start the session

session_start();

$db = new ServerConnection();
$conn = $db->getServerConn();

$unit = new UnitOfWork($conn);

//Modify specific flag
$_SESSION["modifying"] = false;
if($_POST["action"] == "modifying")
{
	$_SESSION["modifying"] = true;
}

//Reservation ID for modification purposes only
$_SESSION["reservationID"] = htmlspecialchars($_POST["reservationID"]);

$wrongTime = "Your End Time must be after your Start Time! Please try again.";
$tooLong = "You cannot reserve for a time of more than 3 hours!";
$currentTime = "You cannot make a reservation on a time that already passed!";

$title = htmlspecialchars($_POST["title"]);
$desc = htmlspecialchars($_POST["description"]);

$passedDate = htmlspecialchars($_POST["dateDrop"]);
$start = htmlspecialchars($_POST["startTime"]);
$end = htmlspecialchars($_POST["endTime"]);

$first = htmlspecialchars($_POST["firstName"]);
$last = htmlspecialchars($_POST["lastName"]);
if(htmlspecialchars($_POST["studentID"]) != "") {
	$_SESSION["sID"] = htmlspecialchars($_POST["studentID"]);
}

$prog = htmlspecialchars($_POST["program"]);
$email = htmlspecialchars($_POST["email"]);

//This holds the number of weeks repeated for a reservation. Defaults to 0 in case modify comes through here
$repeatReservation = 0;
if(htmlspecialchars($_POST["repeatReservation"]) > 0) $repeatReservation = htmlspecialchars($_POST["repeatReservation"]);

//Variable to hold the number of reservations to add in sequence (week repeats)
$reserveCount = 1 + $repeatReservation;

//Getting the ID of the Room 1
//Should Obtain Either 1,2,3,4,5
$rID = htmlspecialchars($_POST["roomID"]);

$student = new StudentMapper($email, $conn);
$reservation = new ReservationMapper();
$room = new RoomMapper($rID, $conn);

$name = $room->getName();

$startEx = explode(":", $start);
$startFloat = ($startEx[0] + ($startEx[1]/60));

$endEx = explode(":", $end);
$endFloat = ($endEx[0] + ($endEx[1]/60));

date_default_timezone_set('US/Eastern');
$ourTime = date('H:i');
$ourTimeEx = explode(":", $ourTime);
$ourTimeFloat = ($ourTimeEx[0] + ($ourTimeEx[1]/60));

$ourDate = date('y-m-d');
$ourDateFormat = strtotime($ourDate);

$dateAmer = date('y-m-d', strtotime($passedDate));
$dateAmerFormat = strtotime($dateAmer);

$dateDiff = $ourDateFormat - $dateAmerFormat;

//Array to store status messages for repeat reservations
$_SESSION["statusArray"] = array();

$_SESSION["selfReservation"] = false;

/*
*	Check if the reservation will be before the current time
*/
if($ourTimeFloat > $startFloat && $dateDiff == 0)
{
	$_SESSION["userMSG"] = $currentTime;
	$_SESSION["msgClass"] = "failure";
}
/*
*	If reservation will last more than 3 hours
*/
else if ( ($endFloat-$startFloat) > 3)
{
	$_SESSION["userMSG"] = $tooLong;
	$_SESSION["msgClass"] = "failure";
}
/*
*	Check if the end time of the reservation will be before the start time
*/
else if ($endFloat <= $startFloat && strcmp("0:00",$end) != 0)
{
	$_SESSION["userMSG"] = $wrongTime;
	$_SESSION["msgClass"] = "failure";
}	
/*
*	Continue with the Reservation normally
*/
else
{
	//Check for presence of more than 3 reservations in the same week 
	//before actually adding the reservation

	$currentReservations = $reservation->getReservations($_SESSION["sID"], $conn);

	for($a = 0; $a < $reserveCount; $a++)
	{
		$res = new ReservationMapper();
		
		//Incase student tries to make a reservation over his own
		$_SESSION["selfReservation"] = false;
		
		//Converting the Date to the Proper Format
		//Should Obtain DD/MM/YYYY	
		$dateEU = date('d-m-Y', strtotime($passedDate . ' + ' . (7*$a) . ' days'));
		$dateAmer = date('m/d/Y', strtotime($passedDate . ' + ' . (7*$a) . ' days'));

		//changed to newStart to facilitate repeat reservations
		$newStart = $dateAmer." ".$start;
		
		if(strcmp("0:00",$end) == 0){
			$dateAmer = date('m/d/Y', strtotime($passedDate . ' + ' . ((7*$a)+1) . ' days'));
		}
		
		$newEnd = $dateAmer." ".$end;
		//Get the list of reservations in same room and on same day
		$availableTimes = $reservation->getReservationsByRoomAndDate($rID, $newStart, 0, $conn);

		//Get start and end time of new reservation, convert the difference to mins to find duration
		$startDate = new DateTime($newStart);
		$endDate = new DateTime($newEnd);
		$_SESSION["userMSG"] = "";

		if(checkWeek($dateEU, $currentReservations) && checkOverlap($startDate, $endDate, $availableTimes, 0)) 
		{
			if($_SESSION["modifying"])
			{
				//Updates reservation instead of adding a new one
				$res->setStartTimeDate($newStart);
				$res->setEndTimeDate($newEnd);
				$res->setTitle($title);
				$res->setDescription($desc);
				$res->setREID($_SESSION["reservationID"]);
				$res->setWait(0);

				$unit->registerDirtyReservation($res);
				$unit->commit();
				//If they've already had 2 reservations, the third will prompt an alert. On confirm, it removes all waitlists for student
				if($_SESSION["confirmedRes"] == 2) {
					$_SESSION["confirmedRes"] = 3;
					$waitListValues = $res->getWaitlistIDByStudent($_SESSION["sID"], 0, $conn);

					$waitStartDate = substr($res->getStartTimeDate(),0,10);

					$dateElementsStart = explode("/", $waitStartDate);
					$dateEU = $dateElementsStart[1]."-".$dateElementsStart[0]."-".$dateElementsStart[2];
					$week = date("W", strtotime($dateEU));

					foreach($waitListValues as $entry) {
						$resTemp = new ReservationMapper();
						$resTemp->setREID($entry->getID());

						//Make sure you only remove waitlists from same week
						//Get date of current waitlist values
						$tempStartDate = substr($entry->getStartTimeDate(),0,10);
						$tempElementsStart = explode("/", $tempStartDate);
						$tempEU = $tempElementsStart[2]."-".$tempElementsStart[1]."-".$tempElementsStart[0];
						$tempDate = date("j-m-Y", strtotime($entry->getStartTimeDate()));
						$tempWeek = date("W", strtotime($tempDate));

						if($week == $tempWeek) {
							
							$unit->registerDeletedReservation($resTemp);
							$unit->commit();
							
						}
					}
				}

				//If they have less than 2 confirmed reservations, once you get off waitlist delete entries that overlap and are on waitlist
				elseif($_SESSION["confirmedRes"] < 2) {
					$_SESSION["confirmedRes"]++;

					//Get waitlist options for student on same day
					$studentsWaitlist = $res->getReservationsBySIDAndDate($_SESSION["sID"], $newStart, $conn);
					
					$tempArray = array();
					foreach($studentsWaitlist as $reservation) {
						array_push($tempArray, $reservation);

						//If overlaps, then remove it
						//Compare new addition's start and end time to other waitList options for that day
						//If overlap occurs, and the reservation is not itself, remove
						if (checkOverlap($startDate, $endDate, $studentsWaitlist, $reservation->getID())){
				 			$resTemp = new ReservationMapper();
							$resTemp->setREID($reservation->getID());

							$unit->registerDeletedReservation($resTemp);

							$roomTemp = new RoomMapper($reservation->getRID(), $conn);
							$roomTemp->setBusy(0);
							$unit->registerDirtyRoom($roomAsked);
						}
						array_pop($tempArray);
					 }
				}
				
				updateWaitlist($res, $rID, $dateAmer, $conn);
				$_SESSION["userMSG"] = "You have successfully updated your Reservation for ".$newStart." to ".$newEnd." in Room ".$name."!";
				$_SESSION["msgClass"] = "success";
			}
			else
			{
				//Just realize display message is in format mm/dd/yyyy
				$res->setSID($_SESSION["sID"]);
				$res->setRID($rID);
				$res->setStartTimeDate($newStart);
				$res->setEndTimeDate($newEnd);
				$res->setTitle($title);
				$res->setDescription($desc);
				$res->setWait(0);
				
				$unit->registerNewReservation($res);

				//If they've already had 2 reservations, the third will prompt an alert. On confirm, it removes all waitlists for student
				if($_SESSION["confirmedRes"] == 2) {
					$_SESSION["confirmedRes"] = 3;
					$waitListValues = $res->getWaitlistIDByStudent($_SESSION["sID"], 0, $conn);

					$waitStartDate = substr($res->getStartTimeDate(),0,10);

					$dateElementsStart = explode("/", $waitStartDate);
					$dateEU = $dateElementsStart[1]."-".$dateElementsStart[0]."-".$dateElementsStart[2];
					$week = date("W", strtotime($dateEU));

					foreach($waitListValues as $entry) {
						$resTemp = new ReservationMapper();
						$resTemp->setREID($entry->getID());

						//Make sure you only remove waitlists from same week
						//Get date of current waitlist values
						$tempStartDate = substr($entry->getStartTimeDate(),0,10);
						$tempElementsStart = explode("/", $tempStartDate);
						$tempEU = $tempElementsStart[2]."-".$tempElementsStart[1]."-".$tempElementsStart[0];
						$tempDate = date("j-m-Y", strtotime($entry->getStartTimeDate()));
						$tempWeek = date("W", strtotime($tempDate));


						if($week == $tempWeek) {
							$unit->registerDeletedReservation($resTemp);
						}
					}
				}

				elseif($_SESSION["confirmedRes"] < 2) {
					$_SESSION["confirmedRes"]++;

					//Get waitlist options for student on same day
					$studentsWaitlist = $res->getReservationsBySIDAndDate($_SESSION["sID"], $newStart, $conn);
					
					$tempArray = array();
					foreach($studentsWaitlist as $reservation) {
						array_push($tempArray, $reservation);

						//If overlaps, then remove it
						//Compare new addtion's start and end time to other waitList options for that day
						//If overlap occurs, and the reservation is not itself, remove
						if (checkOverlap($startDate, $endDate, $studentsWaitlist, $reservation->getID())){
				 			$resTemp = new ReservationMapper();
							$resTemp->setREID($reservation->getID());

							$unit->registerDeletedReservation($resTemp);

							$roomTemp = new RoomMapper($reservation->getRID(), $conn);
							$roomTemp->setBusy(0);
							$unit->registerDirtyRoom($roomAsked);
						}
						array_pop($tempArray);
					 }
				}
				
				if($reserveCount == 1) {
					//Display for single reservation (no repeat)
					$_SESSION["userMSG"] = "You have successfully made a reservation for ".$newStart." to ".$newEnd. " in Room ".$name."!";
					$_SESSION["msgClass"] = "success";
				}
				else
				{
					array_push($_SESSION["statusArray"], "reserved");
				}
			}
		}
		else if ($_SESSION["userMSG"] == "This option overlaps, you've been added to the Waitlist!") 
		{
			if($_SESSION["modifying"])
			{
				$res->setStartTimeDate($newStart);
				$res->setEndTimeDate($newEnd);
				$res->setTitle($title);
				$res->setDescription($desc);
				$res->setREID($_SESSION["reservationID"]);
				$res->setWait(1);
				
				$unit->registerDirtyReservation($res);
			}
			if((!$_SESSION["modifying"])) 
			{
				//Display for single reservation (no repeat)
				$res->setSID($_SESSION["sID"]);
				$res->setRID($rID);
				$res->setStartTimeDate($newStart);
				$res->setEndTimeDate($newEnd);
				$res->setTitle($title);
				$res->setDescription($desc);
				$res->setWait(1);
				
				$unit->registerNewReservation($res);
			}
			if((!$_SESSION["modifying"]) && $reserveCount > 1)
			{
				array_push($_SESSION["statusArray"], "waitlisted");
			}
		}
		elseif($_SESSION["selfReservation"])
		{
			if((!$_SESSION["modifying"]) && $reserveCount == 1)
			{
				$_SESSION["userMSG"] = "You already have a reservation at that time!";
				$_SESSION["msgClass"] = "failure";
			}
			elseif((!$_SESSION["modifying"]) && $reserveCount > 1)
			{
				array_push($_SESSION["statusArray"], "selfreserve");
			}
		}
	}
	//To display message after multireservation
	if($reserveCount > 1)
	{
		$statusString = "| ";
		$count = 1;
		foreach($_SESSION["statusArray"] as &$val)
		{
			if($val == "reserved")
			{
				$statusString .= "Week ". $count . " Reserved | ";
			}
			elseif($val == "waitlisted")
			{
				$statusString .= "Week ". $count . " Waitlisted | ";
			}
			elseif($val == "weeklimited")
			{
				$statusString .= "Week ". $count . " Reserves Exceeded | ";
			}
			elseif($val == "selfreserve")
			{
				$statusString .= "Week ". $count . " Reservation Already Exists |";
			}
			$count++;
		}
		
		$_SESSION["userMSG"] = $statusString;
		$_SESSION["msgClass"] = "success";
	}
}

$_SESSION['roomAvailable'] = false;

$unit->commit();
$db->closeServerConn($conn);

header("Location: ClearRoom.php");
?>