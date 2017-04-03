<?php
use Stark\Mappers\ReservationMapper;
use Stark\RequestModels\EquipmentRequest;
use Stark\Models\Reservation;
use Stark\ModifyReservationSession;
use Stark\RequestModels\ReservationRequestBuilder;
use Stark\TimeValidator;
use Stark\Utilities\ReservationSanitizer;

session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

// Extract parameters from request
$reservationId = $_REQUEST['reservationId'];
$date = $_REQUEST['date'];
$startTime = $_REQUEST['startTime'];
$endTime = $_REQUEST['endTime'];
$title = $_REQUEST['title'];
$equipments = json_decode($_REQUEST['equipment']);
$changedEquipment = $_REQUEST['changedEquipment'] === 'true' ? true : false;
$roomId = intval($_REQUEST['roomId']);

// Convert to equipment requests
$equipmentRequests = [];
foreach ($equipments as $equipment) {
    $equipmentRequests[] = new EquipmentRequest($equipment[0], $equipment[1], $equipment[2]);
}

$reservationMapper = new ReservationMapper();

// Sanitize input data
$reservationSanitizer = new ReservationSanitizer();

/**
 * @var Reservation $reservation
 */
$reservation = $reservationMapper->findByPk($reservationId);

$startTimeDate = "";
$endTimeDate = "";

if ($date == "" || $startTime == "") {
    $startTimeDate = $reservation->getStartTimeDate();
} else {
    $startTimeDate = $reservationSanitizer->convertToDateTime($date, $startTime);
}

if ($date == "" || $endTime == "") {
    $endTimeDate = $reservation->getEndTimeDate();
} else {
    $endTimeDate = $reservationSanitizer->convertToDateTime($date, $endTime);
}

if ($title == "") {
    $title = $reservation->getTitle();
}

if($roomId < 0)

$timeValidationErrors = TimeValidator::validate($startTimeDate, $endTimeDate)->getErrors();

if (!empty($timeValidationErrors)) {
    ?>
    <br>
    <div class="alert alert-danger">
        <?php
        $msg .= "<ul>";
        foreach ($timeValidationErrors as $error) {
            $msg .= '<li>' . $error . '</li>';
        }
        $msg .= "</ul>";
        echo $msg;
        ?>
    </div>

    <?php
    return;
}

$reservationRequestBuilder = new ReservationRequestBuilder();
$reservationRequestBuilder
    ->title($title)
    ->roomId($roomId)
    ->startTimeDate($startTimeDate)
    ->endTimeDate($endTimeDate)
    ->equipmentRequests($equipmentRequests);
$reservationRequest = $reservationRequestBuilder->build();

$modifyReservationSession = new ModifyReservationSession();
$errors = $modifyReservationSession->modify($reservationId, $changedEquipment, $reservationRequest);
if (!empty($errors)) {
    ?>
    <div class="alert alert-danger">
        Could not modify reservation due to conflicts!
    </div>
    <div class="alert alert-info">
        <?php
        $msg .= "<ul>";
        foreach ($errors as $error) {
            $msg .= '<li>' . $error . '</li>';
        }
        $msg .= "</ul>";
        echo $msg;
        ?>
    </div>
    <?php
} else {
    ?>
    <div id="modifyMessage" title="Success">
        <div class="alert alert-success">
            You have successfully modified your reservation!
        </div>
    </div>
    <script>
        // Refresh user reservations
        $(function () {
            userReservations.ajax.reload(function (json) {
                $('#modifyMessage').dialog('close');
                $('#modifyReservationModal').dialog('close');
            }, false);

            $('#calendar').fullCalendar('refetchEvents');
        });

    </script>
    <?php
}
?>