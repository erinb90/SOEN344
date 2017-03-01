<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');
$ReservationMapper = new \Stark\Mappers\ReservationMapper();
$reservations = $ReservationMapper->findAllStudentReservations(Stark\WebUser::getUser()->getUserId());
$RoomMapper = new \Stark\Mappers\RoomMapper();
$userReservation = ["data" => []];
/**
 * @var $Reservation \Stark\Models\Reservation
 */
foreach ($reservations as $Reservation)
{
    $canBeModified = strtotime($Reservation->getStartTimeDate()) > strtotime(date("Y-m-d H:i:s"));
    $userReservation['data'][] = [

        "reid"       => $Reservation->getReservationID(),
        "rid"        => $Reservation->getRoomId(),
        "roomName"   => $RoomMapper->findByPk($Reservation->getRoomId())->getName(),
        "title"      => $Reservation->getTitle(),
        "StartTime"  => date("H:i", strtotime($Reservation->getStartTimeDate())),
        "EndTime"    => date("H:i", strtotime($Reservation->getEndTimeDate())),
        "Date"       => date("Y-m-d", strtotime($Reservation->getStartTimeDate())),
        "modifiable" => $canBeModified
    ];
}
echo json_encode($userReservation);