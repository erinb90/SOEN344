<?php
use Stark\ModifyReservationSession;
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

// Sanitize input data
$reservationSanitizer = new ReservationSanitizer();
$startTimeDate = $reservationSanitizer->convertToDateTime($date, $startTime);
$endTimeDate = $reservationSanitizer->convertToDateTime($date, $endTime);
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

$modifyReservationSession = new ModifyReservationSession();
$errors = $modifyReservationSession->modify($reservationId, $date, $startTimeDate, $endTimeDate, $title);

if (!empty($errors)) {
    ?>
    <div class="alert alert-danger">
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
    <script>
        // Refresh user reservations
        $(function () {
            userReservations.ajax.reload(function (json) {
                $('#modifyMessage').dialog('close');
                $('#modifyReservationModal').dialog('close');
            }, false);
        })

    </script>
    <?php
}
?>