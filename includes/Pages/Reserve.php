<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');


$startTime = $_REQUEST['startTime'];
$endTime = $_REQUEST['endTime'];
$roomid = $_REQUEST['rid'];
$name = $_REQUEST['name'];

$ReservationCreator = new MakeReservationSession();


if($ReservationCreator->reserve(WebUser::getUser(), $roomid, $startTime, $endTime, $name))
{
    ?>
    <div class="alert alert-success">
        You have successfully created your reservation!
    </div>
    <script>

    </script>
    <?php

}
else if(count($ReservationCreator->getConflicts()) > 0)
{

    $conflicts  = $ReservationCreator->getConflicts();
    ?>
    <script>
        $(function(){
            $('#reservationContainerMessage').dialog("close");
            $('#conflictResolutionContainer').dialog({
                width: 800,
                height: 550,
                title: "Conflict Resolution",
                buttons:
                {
                    "Save" : function()
                    {

                        var reid = $("input[name='conflict']:checked").val();

                        if(!reid || reid === undefined)
                        {
                            return;
                        }


                        $.ajax({
                            url: 'CreateWaitlist.php',
                            data: {
                                reid: reid,
                            },
                            error: function ()
                            {
                                $('#conflictResolutionMessage').html("An unknown error occurred");
                            },
                            success: function (data)
                            {
                                $('#conflictResolutionMessage').html(data);
                            }
                        });
                    },


                },
                Close : function()
                {
                    $('#reservationContainerMessage').dialog('destroy');
                }
            })
        })
    </script>

    <div id="conflictResolutionContainer">
        <p class="text-center text-danger">Conflicts Found!</p>
        If you wish to put yourself on a waiting list for the reservations below, click on the appropriate reservation and click on "Save".
        <div>
            <table class="table">
                <thead>
                <tr>
                    <th>Reservation ID</th>
                    <th>User</th>
                    <th>Start Time</th>
                    <th>End Time
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                /**
                 * @var $Reservation ReservationDomain
                 *
                 */
                foreach($conflicts as $reid => $Reservation)
                {
                    $UserMapper = new StudentMapper();
                    $User = $UserMapper->findByPk($Reservation->getSID());
                    ?>
                    <tr>
                        <td><?php echo $reid; ?></td>
                        <td><?php echo $User->getUsername(); ?></td>
                        <td><?php echo $Reservation->getStartTimeDate(); ?></td>
                        <td><?php echo $Reservation->getEndTimeDate(); ?></td>
                        <td><input type="radio" id="conflict" name="conflict" value="<?php echo $reid;?>"></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php


}
else
{
    $errors = $ReservationCreator->getErrors();
    ?>
    <div class="alert alert-danger">
        <?php
        $msg .= "<ul>";
        foreach ($errors as $error)
        {
            $msg .= '<li>' . $error . '</li>';
        }
        $msg .= "</ul>";
        echo $msg;
        ?>
    </div>
    <?php
}