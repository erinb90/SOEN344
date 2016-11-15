<?php
include "../Class/ReservationMapper.php";
include "../Class/RoomMapper.php";
include_once dirname(__FILE__).'/../Utilities/ServerConnection.php';

// Start the session
session_start();

$uow = new UnitOfWork();
$conn = $uow->getServerConn();

$reservation = new ReservationMapper();

$rID = $_POST['roomNum'];

$roomAsked = new RoomMapper($rID, $conn);
$roomAnswer = $roomAsked->checkBusy($rID, $conn);
$roomName = $roomAsked->getName();

if($roomAnswer == 0)
{
	$roomAsked->setBusy(true, $rID, $conn);
	$_SESSION['roomAvailable'] = true;
}
else
{
	$_SESSION["userMSG"] = "This room is being used by another Student!";
	$_SESSION["msgClass"] = "failure";
	$_SESSION['roomAvailable'] = false;
}

header("Location: Home.php");

$uow->closeServerConn($conn);

?>