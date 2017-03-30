<?php
use Stark\Mappers\RoomMapper;
use Stark\Models\Reservation;
use Stark\ReservationRegistry;

session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');
$ReservationRegistry = new ReservationRegistry();
$reservations = $ReservationRegistry->getReservations();
$RoomMapper = new RoomMapper();
$output = array();
//Todo: when loading reservations, we should also remove the people who are on waitlist for timeslots that have already passed.
/**
 * @var $Reservation Reservation
 */
foreach($reservations as $Reservation)
{
    $output[] = array(
        "id" => $Reservation->getReservationID(),
        "resourceId" => $Reservation->getRoomId(),
        "start" => $Reservation->getStartTimeDate(),
        "end" => $Reservation->getEndTimeDate(),
        "title" => $Reservation->getTitle(),
        "roomLocation" => $RoomMapper->findByPk($Reservation->getRoomId())->getLocation(),
        "uid" => $Reservation->getUserId(),
        "reservedOn" => $Reservation->getCreatedOn(),
    );
}
echo json_encode($output);