<?php
 include_once "../Class/RoomMapper.php";
 include_once "../Class/StudentMapper.php";
 include_once "../Class/ReservationMapper.php";
 include_once "../Class/Unit.php";
include '../Utilities/Function.php';
 include_once dirname(__FILE__).'/../Utilities/ServerConnection.php';

//echo get_include_path();
// Start the session
session_start();

$db = new ServerConnection();
$conn = $db->getServerConn();

$unit = new UnitOfWork($conn);
$_SESSION["unit"] = $unit;

if(empty($_POST['rID'])){
	$rID = $_SESSION['roomID'];
}
else{
	$rID = $_POST['rID'];
}

if(empty($_POST['action'])){
	$action = $_SESSION['action'];
}
else{
	$action = $_POST['action'];
}

if(empty($_POST['reID'])){
	$reID = $_SESSION['reID'];
}
else{
	$reID = $_POST['reID'];
}

$reservation = new ReservationMapper($reID, $conn);

//Need start date for reservation being modified
$oldRes = $reservation->getReservation($reID, $conn);
$start = substr($oldRes["startTimeDate"],0,10);
$end = substr($oldRes["endTimeDate"],0,10);

//Reformate start date, to be used in sql statement
$dateElementsStart = explode("/", $start);
$reformatStart = $dateElementsStart[1]."/".$dateElementsStart[2]."/".$dateElementsStart[0];

if($action == "delete")
{	
	//Dropped date from message for the moment since its not being posted - NB
	$date = $_POST['date'];

	$unit->registerDeletedReservation($reservation);
	$unit->commit();
	updateWaitlist($reservation, $rID, $reformatStart, $conn);
	
	$_SESSION["userMSG"] = "You have successfully deleted your Reservation!";
	$_SESSION["msgClass"] = "success";

	$db->closeServerConn($conn);
	header("Location: ClearRoom.php");
}
elseif($action == "modify")
{
	$_SESSION['modify'] = true;
	$_SESSION['reservation'] = $reID;
	
	$unit->commit();
	$db->closeServerConn($conn);
	header("Location: Home.php");
}



?>