<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');
$ReservationMapper = new ReservationMapper();
$reservations = $ReservationMapper->findAllStudentReservations(WebUser::getUser()->getSID());
$RoomMapper = new RoomMapper();
$userReservation = array("data" => array());
/**
 * @var $Reservation ReservationDomain
 */
foreach($reservations as $Reservation)
{
    $canBeModified = strtotime($Reservation->getStartTimeDate()) >  strtotime(date("Y-m-d H:i:s"));
    $userReservation['data'][] = array
    (
        "reid" => $Reservation->getReID(),
        "rid"=>$Reservation->getRid(),
        "roomName" => $RoomMapper->findByPk($Reservation->getRID())->getName(),
        "title" => $Reservation->getTitle(),
        "StartTime" => date("H:i", strtotime($Reservation->getStartTimeDate())),
        "EndTime" => date("H:i", strtotime($Reservation->getEndTimeDate())),
        "Date" => date("Y-m-d", strtotime($Reservation->getStartTimeDate())),
        "modifiable" => $canBeModified
    );
}
echo json_encode($userReservation);