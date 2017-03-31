<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

use Stark\CreateReservationSession;
use Stark\Models\EquipmentRequest;
use Stark\TimeValidator;
use Stark\Utilities\ReservationSanitizer;
use Stark\WebUser;

// Parse incoming request and extract query parameters
$requestParameters = [];
parse_str($_REQUEST['formData'], $requestParameters);
$equipments = json_decode($_REQUEST['equipment']);
$computerAlt = $_REQUEST['computerAlt'] === 'true' ? true : false;
$projectorAlt = $_REQUEST['projectorAlt'] === 'true' ? true : false;

// Convert to equipment requests
$equipmentRequests = [];
foreach ($equipments as $equipment) {
    $equipmentRequests[] = new EquipmentRequest($equipment[0], $equipment[1]);
}

$user = WebUser::getUser();

// Sanitize input data
$reservationSanitizer = new ReservationSanitizer();
$startTimeDate = $reservationSanitizer->convertToDateTime($requestParameters['rDate'], $requestParameters['startTime']);
$endTimeDate = $reservationSanitizer->convertToDateTime($requestParameters['rDate'], $requestParameters['endTime']);
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

// TODO : Prevent user from creating duplicate reservations (even if waitlisted)
// Create reservation session
$ReservationSession = new CreateReservationSession(
    $user,
    $requestParameters['roomID'],
    $startTimeDate,
    $endTimeDate,
    $requestParameters['title'],
    $requestParameters['repeatReservation'],
    $computerAlt,
    $projectorAlt,
    $equipmentRequests);

if ($ReservationSession->reserve()) {
    ?>
    <div id="successReservation" title="Success">
        <div class="alert alert-success">
            You have successfully created your reservation!
        </div>
    </div>
    <script>
        $(function () {
            $('#myModal').dialog("destroy");
            $('#successReservation').dialog({
                width: 400
            });
        })
        // Refresh user reservations
        userReservations.ajax.reload(function (json) {
        }, false);
    </script>
    <?php
} else if (count($ReservationSession->getErrors()) > 0) {
    $waitListPosition = $ReservationSession->getWaitListPosition();
    $conflicts = $ReservationSession->getErrors();
    ?>
    <div id="waitlistReservation" style="display: none;" title="Waitlist">
        <div id="reservationResult">
            <div class="alert alert-warning">
                Your reservation has been wait listed at position
                <?php echo $waitListPosition ?> due to conflicts!
            </div>
            <div class="alert alert-info">
                <?php
                $msg .= "<ul>";
                foreach ($conflicts as $conflict) {
                    $msg .= '<li>' . $conflict . '</li>';
                }
                $msg .= "</ul>";
                echo $msg;
                ?>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('#myModal').dialog("destroy");
            $('#waitlistReservation').dialog({
                width: 400
            });
            userReservations.ajax.reload(function (json) {
            }, false);

            $('#calendar').fullCalendar('refetchEvents'  );
        })
    </script>
    <?php
}
?>