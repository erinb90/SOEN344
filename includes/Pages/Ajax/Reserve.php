<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

use Stark\CreateReservationSession;
use Stark\RequestModels\EquipmentRequest;
use Stark\TimeValidator;
use Stark\Utilities\ReservationSanitizer;
use Stark\WebUser;
use Stark\RequestModels\ReservationRequestBuilder;

// Parse incoming request and extract query parameters
$requestParameters = [];
parse_str($_REQUEST['formData'], $requestParameters);
$equipments = json_decode($_REQUEST['equipment']);

// Convert to equipment requests
$equipmentRequests = [];
foreach ($equipments as $equipment) {
    $equipmentRequests[] = new EquipmentRequest($equipment[0], $equipment[1], $equipment[2]);
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

$reservationRequestBuilder = new ReservationRequestBuilder();
$reservationRequestBuilder
    ->title($requestParameters['title'])
    ->userId($user->getUserId())
    ->roomId($requestParameters['roomID'])
    ->startTimeDate($startTimeDate)
    ->endTimeDate($endTimeDate)
    ->recurrences($requestParameters['repeatReservation'])
    ->equipmentRequests($equipmentRequests);
$reservationRequest = $reservationRequestBuilder->build();

// Create reservation session
$ReservationSession = new CreateReservationSession($reservationRequest);
$statusCode = $ReservationSession->reserve();
if ($statusCode === CreateReservationSession::SUCCESS) {
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
        $(function(){
            userReservations.ajax.reload(function (json) {

                //unlock room
                $.ajax({
                    type: "POST",
                    url: "Lock.php", //file name
                    dataType: "json",
                    data: {
                        action: "unlock",
                        roomID: "<?php echo  $requestParameters['roomID']; ?>"
                    },
                    success: function (data) {

                        console.log(data);
                        if (data.success) {
                            // stop timer
                            CCOUNT = data.secondsDefault;
                            cdpause();
                        }
                        else {
                            $('#lockMessageModal').dialog({

                                width: 300,
                                height: 200
                            });

                            $('#lockMessage').html(data.error);

                            return;
                        }


                    },
                });

            }, false);
        })

    </script>
    <?php
} else {
    $waitListPosition = $ReservationSession->getWaitListPosition();
    $conflicts = $ReservationSession->getErrors();
    $displayMessage = "Something went wrong.";
    $alertClass = "alert-danger";
    switch ($statusCode){
        case CreateReservationSession::WAITLIST:
            $displayMessage = "Your reservation has been wait listed at position " . $waitListPosition . " due to conflicts!";
            $alertClass = "alert-warning";
            break;
        case CreateReservationSession::ERROR:
            $displayMessage = "Failed to create reservation.";
            $alertClass = "alert-danger";
            break;
        default:
            break;
    }
    ?>
    <div id="waitlistReservation" style="display: none;" title="Waitlist">
        <div id="reservationResult">
            <div class="alert <?php echo $alertClass ?>">
                <?php echo $displayMessage ?>
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

                //unlock room
                $.ajax({
                    type: "POST",
                    url: "Lock.php", //file name
                    dataType: "json",
                    data: {
                        action: "unlock",
                        roomID: "<?php echo  $requestParameters['roomID']; ?>"
                    },
                    success: function (data) {

                        console.log(data);
                        if (data.success) {
                            // stop timer
                            CCOUNT = data.secondsDefault;
                            cdpause();
                        }
                        else {
                            $('#lockMessageModal').dialog({

                                width: 300,
                                height: 200
                            });

                            $('#lockMessage').html(data.error);

                            return;
                        }


                    },
                });

            }, false);

            $('#calendar').fullCalendar('refetchEvents'  );
        })
    </script>
    <?php
}
?>