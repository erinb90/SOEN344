<?php

include "../Class/RoomMapper.php";
include "../Class/StudentMapper.php";
include "../Class/ReservationMapper.php";

include "../Class/Unit.php";
include_once dirname(__FILE__).'/../Utilities/ServerConnection.php';

// Start the session
session_start();

$db = new ServerConnection();
$conn = $db->getServerConn();

$unit = new UnitOfWork($conn);

$rID = $_POST['rID'];
$action = $_POST['action'];
$reID = $_POST['reID'];

//In case the User clicks out of the Modal
$_SESSION['roomID'] = $rID;
$_SESSION['action'] = $action;
$_SESSION['reID'] = $reID;

$roomAsked = new RoomMapper($rID, $conn);
$roomAnswer = $roomAsked->checkBusy($rID, $conn);
$roomName = $roomAsked->getName();

//Temporary fix until datePicker is changed (?)
$passedDate = htmlspecialchars($_POST["dateDrop"]);
$_SESSION['selectedDate'] = $passedDate;

if($roomAnswer == 0)
{
	$roomAsked->setBusy(true);
	$unit->registerDirtyRoom($roomAsked);
	$_SESSION['roomReserveID'] = $rID;
}
else
{
	$_SESSION["userMSG"] = "Room ".$roomName." is being used by another Student!";
	$_SESSION["msgClass"] = "failure";
	$_SESSION['roomAvailable'] = false;
}

$unit->commit();
$db->closeServerConn($conn);

if($action == "modify" or $action == "delete")
{
	if($roomAnswer == 0)
	{
		header("Location: ModifyReservation.php");
	}
	else
	{
		header("Location: Home.php");
	}
}
else
{
	if($roomAnswer == 0)
	{
		$_SESSION['roomAvailable'] = true;
	}
	header("Location: Home.php");
}
?>