<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

use Stark\CreateReservationSession;
use Stark\Enums\EquipmentType;
use Stark\Models\EquipmentRequest;
use Stark\WebUser;

// Parse incoming request and extract query parameters
$requestParameters = [];
parse_str($_REQUEST['formData'], $requestParameters);
$equipments = json_decode($_REQUEST['equipment']);

// Convert to equipment requests
$equipmentRequests = [];
foreach ($equipments as $equipment){
    $equipmentRequests[] = new EquipmentRequest($equipment[0], $equipment[1]);
}

$user = WebUser::getUser();

// TODO : Perform time sanitizing using regex
$startTimeDateAsString = $requestParameters['rDate'] . " " . $requestParameters['startTime'] . ":00";
$startTimeDate = date("Y-m-d H:i:s", strtotime($startTimeDateAsString));

$endTimeDateAsString = $requestParameters['rDate'] . " " . $requestParameters['endTime'] . ":00";
$endTimeDate = date("Y-m-d H:i:s", strtotime($endTimeDateAsString));

$timeValidationErrors = \Stark\TimeValidator::validate($startTimeDate, $endTimeDate)->getErrors();

if (!empty($timeValidationErrors)) {
    foreach ($timeValidationErrors as $error) {
        echo $error;
    }
    return;
}

// TODO : Fix remaining old code to work with new reservation session
// TODO : Prevent user from creating duplicate reservations (even if waitlisted)
// Create reservation session
$ReservationSession = new CreateReservationSession(
    $user,
    $requestParameters['roomID'],
    $startTimeDate,
    $endTimeDate,
    $requestParameters['title'],
    $requestParameters['repeatReservation'],
    $equipmentRequests);

if ($ReservationSession->reserve()) {
    ?>
    <div id="successReservation">
        <div class="alert alert-success">
            You have successfully created your reservation!
        </div>
    </div>
    <script>

        $(function () {

            $('#myModal').dialog("destroy");

            $('#successReservation').dialog({
                title: 'Success',
                width: 400
            });
        })

    </script>
    <?php

} else if (count($ReservationSession->getErrors()) > 0) {
    $conflicts = $ReservationSession->getErrors();
    ?>
    <div id="successReservation">
        <div class="alert alert-success">
            Your reservation has been wait listed due to conflicts!
        </div>
        <div>
            Reasons for conflict:
            <br/>
            <?php
            foreach ($conflicts as $conflict){
                echo $conflict . "<br/>";
            }
            ?>
        </div>
    </div>
    <script>
        $(function () {

            $('#myModal').dialog("destroy");

            $('#successReservation').dialog({
                title: 'Waitlist',
                width: 400
            });
        })

    </script>
    <?php
    exit;
    // TODO : Refactor
    ?>
    <script>
        $(function () {
            $('#conflictResolutionContainer').dialog({
                width: 800,
                height: 550,
                title: "Conflict Resolution",
                modal: true,
                buttons: {
                    "Save": function () {

                        var reid = $("input[name='conflict']:checked").val();

                        if (!reid || reid === undefined) {
                            return;
                        }


                        $.ajax({
                            url: 'CreateWaitlist.php',
                            data: {
                                reid: reid,
                            },
                            error: function () {
                                $('#conflictResolutionMessage').html("An unknown error occurred");
                            },
                            success: function (data) {
                                $('#conflictResolutionMessage').html(data);
                            }
                        });
                    },


                },
                Close: function () {
                    $('#reservationContainerMessage').dialog('destroy');
                }
            })
        })
    </script>

    <div id="conflictResolutionContainer" style="display: none;">
        <p class="text-center text-danger">Conflicts Found!</p>
        If you wish to put yourself on a waiting list for the reservations below, click on the appropriate reservation
        and click on "Save".
        <div>
            <table class="table">
                <thead>
                <tr>
                    <th>Reservation ID</th>
                    <th>User</th>
                    <th>Start Time</th>
                    <th>End Time
                    <th>Room</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                /**
                 * @var $Reservation ReservationDomain
                 *
                 */
                foreach ($conflicts as $reid => $Reservation) {
                    $UserMapper = new StudentMapper();
                    /**
                     * @var StudentDomain $User
                     */
                    $User = $UserMapper->findByPk($Reservation->getSID());

                    $RoomMapper = new RoomMapper();

                    /**
                     * @var RoomDomain $RoomDomain
                     */
                    $RoomDomain = $RoomMapper->findByPk($Reservation->getRID());
                    ?>
                    <tr>
                        <td><?php echo $Reservation->getReID(); ?></td>
                        <td><?php echo $User->getEmailAddress(); ?></td>
                        <td><?php echo $Reservation->getStartTimeDate(); ?></td>
                        <td><?php echo $Reservation->getEndTimeDate(); ?></td>
                        <td><?php echo $RoomDomain->getName(); ?> </td>
                        <td><input type="radio" id="conflict" name="conflict" value="<?php echo $reid; ?>"></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php


} else {
    $errors = $ReservationSession->getErrors();
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
}