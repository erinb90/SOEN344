<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

use Stark\Mappers\LoanContractMapper;
use Stark\Mappers\ReservationMapper;
use Stark\Mappers\RoomMapper;
use Stark\Utilities\ReservationManager;


$ReservationMapper = new ReservationMapper();
$reservations = $ReservationMapper->findAllStudentReservations(Stark\WebUser::getUser()->getUserId());
$RoomMapper = new RoomMapper();
$LoanContractMapper = new LoanContractMapper();
$reservationManager = new ReservationManager();


$userReservation = ["data" => []];
/**
 * @var $Reservation \Stark\Models\Reservation
 */
foreach ($reservations as $Reservation)
{

    $LoanContract = $LoanContractMapper->findByReservationId($Reservation->getReservationID());

    $canBeModified = strtotime($Reservation->getStartTimeDate()) > strtotime(date("Y-m-d H:i:s"));

    $waitListPosition = $reservationManager->getWaitListPosition($Reservation->getReservationID());

    $displayPosition = "-";
    if($waitListPosition !== -1){
        $displayPosition = $waitListPosition;
    }

    $userReservation['data'][] = [

        "reid"       => $Reservation->getReservationID(),
        "rid"        => $Reservation->getRoomId(),
        "roomName"   => $RoomMapper->findByPk($Reservation->getRoomId())->getName(),
        "title"      => $Reservation->getTitle(),
        "StartTime"  => date("H:i", strtotime($Reservation->getStartTimeDate())),
        "EndTime"    => date("H:i", strtotime($Reservation->getEndTimeDate())),
        "Date"       => date("Y-m-d", strtotime($Reservation->getStartTimeDate())),
        "Waiting"    => $Reservation->isIsWaited(),
        "canModify"  => $canBeModified,
        "hasEquipment" => $LoanContract!= null ? true : false,
        "WaitListPosition" => $displayPosition
    ];
}
echo json_encode($userReservation);