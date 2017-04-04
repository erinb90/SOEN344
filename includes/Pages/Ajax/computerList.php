<?php
use Stark\TimeValidator;
use Stark\Utilities\EquipmentFinder;
use Stark\Utilities\ReservationSanitizer;

require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

$date = $_REQUEST['date'];
$startTime = $_REQUEST['startTime'];
$endTime = $_REQUEST['endTime'];
$reservationId = $_REQUEST['reservationId'];
if ($reservationId == null) {
    $reservationId = -1;
} else {
    $reservationId = intval($_REQUEST['reservationId']);
}

if($date == ""){
    $date = date("Y-m-d");
}

// Sanitize input data
$reservationSanitizer = new ReservationSanitizer();
$startTimeDate = $reservationSanitizer->convertToDateTime($date, $startTime);
$endTimeDate = $reservationSanitizer->convertToDateTime($date, $endTime);
$timeValidationErrors = TimeValidator::validate($startTimeDate, $endTimeDate)->getErrors();

$EquipmentCatalog = new \Stark\EquipmentCatalog();
$EquipmentFinder = EquipmentFinder::find($startTimeDate, $endTimeDate);

$equipment = ["data" => []];

/**
 * @var $Computer \Stark\Models\Computer
 *
 */
foreach ($EquipmentCatalog->getAllEquipment()["computers"] as $Computer)
{
    $equipmentAvailable = $EquipmentFinder->equipmentAvailable($Computer->getEquipmentId(), $reservationId) ? "Yes" : "No";
    $equipment['data'][] = [

        "EquipmentId" => $Computer->getEquipmentId(),
        "Manufacturer" => $Computer->getManufacturer(),
        "ProductLine" =>$Computer->getProductLine(),
        "Description" => $Computer->getDescription(),
        "Ram" => $Computer->getRam(),
        "Cpu" => $Computer->getCpu(),
        "Available" => $equipmentAvailable
    ];
}


echo json_encode($equipment);