<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

use Stark\CreateReservationSession;
use Stark\WebUser;

// Parse incoming request and extract query parameters
$requestParameters = [];
parse_str($_REQUEST['formData'], $requestParameters);
$equipmentIds = $_REQUEST['equipment'];

// TODO : Fix remaining old code to work with new reservation session

// TODO : getUser() is returning null
// Create reservation session
$ReservationSession = new CreateReservationSession(
    WebUser::getUser(),
    $requestParameters['roomID'],
    $requestParameters['startTime'],
    $requestParameters['endTime'],
    $requestParameters['title'],
    $equipmentIds);

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

} else if (count($ReservationSession->getConflicts()) > 0) {
    // TODO : Refactor
    exit;
    $conflicts = $ReservationSession->getConflicts();
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