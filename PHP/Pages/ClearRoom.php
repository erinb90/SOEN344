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

$rID = $_SESSION['roomID'];

$roomAsked = new RoomMapper($rID, $conn);
$name = $roomAsked->getName();

$roomAsked->setBusy(0);
$unit->registerDirtyRoom($roomAsked);

$_SESSION['roomAvailable'] = false;

date_default_timezone_set('US/Eastern');
$ourDate = time() - strtotime("today");

//timestamp for 10 seconds in the future when the button should be cleared
$_SESSION['cleared'] = $ourDate+30;

$unit->commit();
$db->closeServerConn($conn);

header("Location: Home.php");
?>