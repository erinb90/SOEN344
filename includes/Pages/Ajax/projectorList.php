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

$equipment = ["data" => []];

/**
 * @var $Projector \Stark\Models\Projector
 *
 */

$EquipmentFinder = EquipmentFinder::find($startTimeDate, $endTimeDate);
foreach ($EquipmentCatalog->getAllEquipment()["projectors"] as $Projector) {

    $equipmentAvailable = $EquipmentFinder->equipmentAvailable($Projector->getEquipmentId(), $reservationId) ? "Yes" : "No";
    $equipment['data'][] = [

        "EquipmentId" => $Projector->getEquipmentId(),
        "Manufacturer" => $Projector->getManufacturer(),
        "ProductLine" => $Projector->getProductLine(),
        "Description" => $Projector->getDescription(),
        "Display" => $Projector->getDisplay(),
        "Resolution" => $Projector->getResolution(),
        "Available" => $equipmentAvailable
    ];
}
echo json_encode($equipment);