<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php';

use Stark\EquipmentFinder;
use Stark\RoomDirectory;
use Stark\Utilities;
use Stark\WebUser;

$RoomDirectory = new RoomDirectory();


// just examples below

$EquipmentFinder = EquipmentFinder::find("2017-02-28 13:00:00", "2017-02-28 19:00:00" );

echo $EquipmentFinder->quantityOfLoanedEquipment(10);

print_r(Utilities::getDateRepeats("2017-02-28 13:00:00", "2017-02-28 13:00:00", 1));

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Room Reserver</title>

    <!-- Bootstrap Core CSS -->
    <link href="../../CSS/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../../CSS/landing-page-Registration.css" rel="stylesheet">

    <!-- Table CSS -->
    <link href="../../CSS/Table.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="../../Javascript/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../../Javascript/bootstrap.min.js"></script>

    <!--jQuery stuff-->
    <!--Try to update to new jquery, doesn't seem to work with jquery 3.1.1-->
    <link rel="stylesheet" href="../../plugins/jquery-ui/jquery-ui.min.css">
    <!-- All Javascript for Home.php page -->

    <!--
    <script src="../../Javascript/Home.js"></script>

    -->
    <script src="../../plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Google Web Font Format for title -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Abel" rel="stylesheet">


    <!-- DataTables CSS -->
    <link href="../../plugins/datatables/media/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Buttons Extension -->
    <link href="../../plugins/datatables/extensions/Buttons/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="../../plugins/datatables/extensions/Buttons/css/buttons.bootstrap.min.css" rel="stylesheet">

    <!-- DataTable Select Extension -->
    <link href="../../plugins/datatables/extensions/Select/css/select.dataTables.min.css" rel="stylesheet">


    <!-- DataTables JavaScript -->
    <script src="../../plugins/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables/media/js/dataTables.bootstrap.min.js"></script>
    <!-- DataTable extensions -->
    <script src="../../plugins/datatables/extensions/Buttons/js/dataTables.buttons.js"></script>
    <script src="../../plugins/datatables/extensions/Buttons/js/buttons.bootstrap.min.js"></script>
    <script src="../../plugins/datatables/extensions/Select/js/dataTables.select.min.js"></script>
    <script src="../../plugins/datatables/extensions/Buttons/js/buttons.flash.js"></script>

    <script>

        //todo: needs some refactoring

        var CCOUNT = 120;

        var t, count;

        function cddisplay()
        {
            // displays time in span
            console.log(count);
            $('#timer').html(count);
        }
        ;

        function countdown()
        {
            // starts countdown
            cddisplay();
            if (count == 0)
            {
                $('#myModal').dialog("close");
            }
            else
            {
                count--;
                t = setTimeout("countdown()", 1000);
            }
        }
        ;

        function cdpause()
        {
            // pauses countdown
            clearTimeout(t);
        }
        ;

        function cdreset()
        {
            // resets countdown
            cdpause();
            count = CCOUNT;
            cddisplay();
        }
        ;


        $(function ()
        {



            // add the date picker
            $('input#rDate').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate   : 0
            });


            //what happens when you click on the make reserve button
            $(document).on('click', '#makeReserve', function ()
            {
                //reset timer
                cdreset();

                var roomid = $('#roomOptions').val();
                var roomName = $('#roomOptions option[value=' + roomid + ']').text();
                $('#roomName').val(roomName);
                $('#roomID').val(roomid);

                // lock room
                $.ajax({
                    type    : "POST",
                    url     : "Lock.php", //file name
                    data    : {
                        action: "lock",
                        roomID: roomid
                    },
                    success : function (data)
                    {
                        // start countdown
                        countdown();
                    },
                    complete: function ()
                    {
                    },
                    error   : function ()
                    {
                        alert("Cannot lock room. Network error.");
                        return;
                    }
                });

                // open modal
                $('#myModal').dialog({
                    width      : 800,
                    modal      : true,
                    show       : 'fade',
                    title      : 'Make Reservation',
                    beforeClose: function (event, ui)
                    {
                        //unlock room
                        $.ajax({
                            type    : "POST",
                            url     : "Lock.php", //file name
                            data    : {
                                action: "unlock",
                                roomID: roomid
                            },
                            success : function (data)
                            {
                                // stop timer
                                cdpause();
                            },
                            complete: function ()
                            {
                            },
                            error   : function ()
                            {
                            }
                        });
                        $(this).dialog("destroy");
                    }
                });
            });


            // when clicking on profile link
            $(document).on('click', '#second-r', function ()
            {

                $('#profilemyModal').dialog({
                    width: 800,
                    modal: true,
                    title: 'My Profile'
                });
            });

            // when saving profile information
            $(document).on('click', '#submitProfile', function ()
            {

                $clicker = $(this);
                var originalText = $clicker.text();
                $clicker.text('Updating...');
                $clicker.addClass('disabled');
                var ser = $('form#profileForm').serialize();

                $('#resultsProfile').html("");

                console.log(ser);

                $.ajax({
                    type    : "POST",
                    url     : "UpdateProfile.php", //file name
                    data    : ser,
                    success : function (data)
                    {
                        $clicker.text(originalText);
                        $('#resultsProfile').html(data);
                    },
                    complete: function ()
                    {
                        $clicker.text(originalText);
                        $clicker.removeClass('disabled');
                    },
                    error   : function ()
                    {
                        $clicker.text(originalText);
                    }
                });
            });


            $(document).on('click', '#createReservation', function ()
            {
                $form = $('#reservationForm');

                $clicker = $(this);
                var originalText = $clicker.text();
                $clicker.text('Submitting...');
                $clicker.addClass('disabled');
                var ser = $form.serialize();

                $('#resultsReservation').html("");

                console.log(ser);

                $.ajax({
                    type    : "POST",
                    url     : "Reserve.php", //file name
                    data    : ser,
                    success : function (data)
                    {
                        $clicker.text(originalText);
                        $('#resultsReservation').html(data);
                    },
                    complete: function ()
                    {
                        $clicker.text(originalText);
                        $clicker.removeClass('disabled');
                    },
                    error   : function ()
                    {
                        $clicker.text(originalText);
                    }
                });

            });

            $(document).on('click', '#third-r', function ()
            {

                $('#myReservationsModal').dialog({
                    width: 800,
                    modal: true,
                    title: 'My Reservations'
                });
            });


            userReservations = $('#reservationsTable').DataTable({
                "processing"   : true,
                "destroy"      : true,
                "serverSide"   : false,
                "displayLength": 25,
                "ajax"         : {
                    "url" : 'StudentReservations.php',
                    "type": "POST",
                },
                "columns"      : [
                    {"data": "reid"},
                    {"data": "title"},
                    {"data": "rid"},
                    {"data": "roomName"},
                    {"data": "Date"},
                    {"data": "StartTime"},
                    {"data": "EndTime"},
                    {
                        'render' : function (data, type, row)
                        {
                            if (row.modifiable)
                            {
                                return '<button id="modifyReservation" name="modifyReservation" type="button" class="btn btn-outline btn-primary btn-square btn-sm">Modify</button>' +
                                    ' <button id="cancelReservation" name="cancelReservation" type="button" class="btn btn-outline btn-danger btn-square btn-sm">Cancel</button>';
                            }
                            else
                            {
                                return "-";
                            }
                        },
                        className: "dt-center"
                    }
                ],
                'order'        : [[0, "asc"]],
                columnDefs     : [{
                    orderable: false,
                    targets  : [5]
                }],
            });


        })
    </script>
</head>
<body>

<!--<body onload="lockoutSubmit(document.getElementById('makeReserve'))"> -->
<!-- Navigation -->
<ul class="topnav" id="myTopnav">
    <li><a class="nav" href="../../logout.php"><span style="font-color:white">Log Out</span></a></li>
    <li><a class="nav" id="second-r" href="#">My Profile</a></li>
    <li><a class="nav" id="third-r" href="#">My Reservations</a></li>
    <li><a class="nav" href="https://my.concordia.ca/psp/upprpr9/EMPLOYEE/EMPL/h/?tab=CU_MY_FRONT_PAGE2">MyConcordia</a>
    </li>
</ul>

<!-- Header -->
<div class="intro-header">

    <div class="container">
        <div class="row">
            <!-- Id space to confirm if the data was saved or not -->
            <div>
                <?php

                ?>
            </div>

            <!-- class greeting -->
            <div class="greeting">
            </div>

            <!-- Div for datepicker -->
            <div id="datepickerContainer" style="width:1200px;">
                <h1 class="title">THE FORCE AWAKENS</h1>
                <h3 class="subtitle">Room Reserver</h3>
                <div id="datepickerInline"></div>
                <br><br>
                <div id="reserveButton">

                    <div>
                        <select id="roomOptions" class="btn btn-default btn-lg network-name" name="rID">
                            <?php

                            /**
                             * @var \Stark\Models\Room $RoomDomain
                             */
                            foreach ($RoomDirectory->getRooms() as $RoomDomain)
                            {
                                ?>

                                <option value="<?php echo $RoomDomain->getRoomId(); ?>"><?php echo $RoomDomain->getName(); ?></option>

                                <?php
                            }
                            ?>
                        </select>
                        <!-- Hidden input for temporary datepicker fix-->
                        <input type="hidden" readonly="readonly" type="text" class="form-control" name="dateDrop"
                                id="dateDrop" placeholder="Nothing" />
                        <button type="button" class="btn btn-default btn-lg" id="makeReserve"><span
                                    class="network-name">Make a Reservation</span></button>

                    </div>
                    <br>

                </div>
                <br>
                <div id="legendContainer">
                    <h6 class="legendTitle">LEGEND</h6>
                    <h6 class="green">Your Booking</h6>
                    <h6 class="red">Booked</h6>
                </div>
            </div>

            <!-- Reservation Modal -->
            <div id="myModal" role="dialog" style="display: none;">
                <div>
                    <!-- Modal content-->
                    <div>

                        <form id="reservationForm">
                            <div class="modal-body">


                                <div class="form-group">
                                    <div class="timer" style="color:red;text-align: center;">Reservation closes in <span
                                                id="timer">-</span> seconds!
                                    </div>
                                    <label>Title of Reservation</label>
                                    <input required type="text" class="form-control" placeholder="Enter a Title"
                                            name="title">
                                </div>
                                <div class="form-group">
                                    <label>Description of Reservation</label>
                                    <textarea style="resize:none;" rows="3" cols="50"
                                            placeholder="Describe the Reservation here..." class="form-control"
                                            name="description"></textarea>
                                </div>
                                <!-- Time slots should be inserted here-->
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="text" class="form-control" name="rDate" id="rDate" /> <br>
                                    <label>Start Time: (mm:ss)</label>
                                    <input type="text" class="form-control" id="startTime" name="startTime">
                                    <label>End Time: (mm:ss)</label>
                                    <input type="text" class="form-control" id="endTime" name="endTime">
                                    <label>Room:</label>
                                    <input readonly="readonly" class="form-control" id="roomName" name="roomName" />
                                    <input hidden name="roomID" id="roomID">
                                </div>
                                <div class="form-group">
                                    <label>Repeat Reservation for:</label>
                                    <select id="repeatReservation" name="repeatReservation">
                                        <option value="0">
                                            No Repeat
                                        </option>
                                        <option value="1">1 Week</option>
                                        <option value="2">2 Weeks</option>
                                        <option value="3">3 Weeks</option>
                                    </select>
                                </div>
                                <button type="button" id="createReservation" class="btn btn-default btn-success btn-block">Submit</button>

                            </div>
                        </form>
                        <div id="resultsReservation"></div>
                    </div>
                </div>
            </div>

            <!-- Edit Reservation Modal -->
            <div class="modal fade" id="editModal" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4>Please edit the specifications of your reservation</h4>
                            <div class="timer2" style="color:red;text-align: center;">Reservation closes in <span
                                        id="timer2"></span> seconds!
                            </div>
                        </div>
                        <div class="modal-body">
                            <form id="formEdit" action="Reserve.php" method="post">
                                <input readonly="readonly" type="hidden" class="form-control" name="reservationID"
                                        id="reservationID" value="<?php echo $modReserve['reservationID']; ?>" />
                                <input readonly="readonly" type="hidden" class="form-control" name="roomID"
                                        id="reservationID" value="<?php echo $modReserve['roomID']; ?>" />
                                <div class="form-group">
                                    <label>Room Name</label>
                                    <input readonly="readonly" type="text" class="form-control" name="roomID"
                                            id="reservationID" value="<?php echo $roomChosen; ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Title of Reservation</label>
                                    <input type="text" class="form-control" placeholder="Enter a Title" name="title"
                                            value="<?php echo $modReserve['title']; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Description of Reservation</label>
                                    <textarea rows="3" cols="50" placeholder="Describe the Reservation here..."
                                            class="form-control"
                                            name="description"><?php echo $modReserve['description']; ?></textarea>
                                </div>
                                <!-- Time slots should be inserted here-->
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input readonly="readonly" type="text" class="form-control" name="dateDrop"
                                            id="dateDrop" value="<?php echo $modDate[0]; ?>" /> <br>
                                    <label>Start Time:</label> <select name="startTime">
                                        <?php

                                        ?>
                                    </select>&nbsp &nbsp &nbsp <label>End Time:</label> <select name="endTime">
                                        <?php

                                        ?>
                                    </select>&nbsp &nbsp &nbsp

                                    <input readonly="readonly" id="roomOptionsMod" class="roomNum" name="roomName"
                                            value="<?php //if($roomReserve != NULL) echo $roomReserve->getName(); ?>" />
                                    <input hidden name="roomID"
                                            value="<?php //if($roomReserve != NULL) echo $roomReserve->getRID(); ?>">
                                </div>
                                <button type="submit" name="action" value="modifying"
                                        class="btn btn-default btn-success btn-block">Submit
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Modal -->
            <div id="profilemyModal" style="display: none;">
                <div>
                    <!-- Modal content-->
                    <div>
                        <div class="modal-body">
                            <form id="profileForm">
                                <div class="form-group">
                                    <label>First name</label>
                                    <input readonly="readonly" type="text" class="form-control" name="firstName" id="firstName"
                                            placeholder="First Name"
                                            value="<?php echo WebUser::getUser()->getFirstName(); ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Last name</label>
                                    <input readonly="readonly" type="text" class="form-control" name="lastName" id="lastName"
                                            placeholder="Last Name"
                                            value="<?php echo WebUser::getUser()->getLastName(); ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Student ID</label>
                                    <input readonly="readonly" type="text" class="form-control" name="studentID"
                                            placeholder="Student ID" value="<?php echo WebUser::getUser()->getStudentId(); ?>" />
                                </div>

                                <div class="form-group">
                                    <label>Old Password</label>
                                    <input type="password" class="form-control" name="oldPass"
                                            placeholder="Old Password" />
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="newPass"
                                            placeholder="New Password" />
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="text" class="form-control" name="uEmail" id="uEmail"
                                            placeholder="Email Address" value="<?php echo WebUser::getUser()->getUserName(); ?>" />
                                </div>


                                <button type="button" id="submitProfile" class="btn btn-default btn-success btn-block">Submit</button>
                            </form>
                            <br>
                            <div id="resultsProfile"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Reservations Modal -->
            <div class="modal fade" id="reservationmyModal" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4>Your Reservations</h4>

                        </div>
                        <div class="modal-body">
                            <h5 id="legendC">Confirmed Reservations</h5>
                            <h5 id="legendW">Waitlisted Reservations</h5><br>
                            <?php
                            /*
                            $conn = $db->getServerConn();

                            $count = 1;
                            foreach($studentReservations as &$singleReservation)
                            {
                                $active = new RoomMapper($singleReservation["roomID"], $conn);
                                $activeRoom = $active->getName();
                                $deleteButton = '<button type="Submit" name="action" value = "delete" id="deleteButton" class="center btn btn-default"> Delete Reservation '.$count.'</button>';
                                $modifyButton = '<br><button type="Submit" data-target="myModal" id = "modifyButton" name="action" value = "modify" class="center btn btn-default"> Modify Reservation '.$count.'</button>';
                                $hidden = '<input type="hidden" name="reID" value="'.$singleReservation["reservationID"].'"></input>';
                                $hidden2 ='<input type="hidden" name="rID" value="'.$singleReservation['roomID'].'"></input>';
                                $startDateTime = explode(" ", $singleReservation["startTimeDate"]);
                                $endDateTime = explode(" ", $singleReservation["endTimeDate"]);
                                $waitlisted = explode(" ", $singleReservation["waitlisted"]);

                                date_default_timezone_set('US/Eastern');
                                $timeNow = date("Y-m-d H:i:s");

                                if(strtotime($singleReservation["startTimeDate"]) > strtotime($timeNow)) {
                                    echo "<form id='myReservationform' action='CheckRoomAvailable.php' method='post'>";
                                    if ($waitlisted[0] == "1") {
                                        echo "<section class = 'leftcolumnW'>";
                                            echo $hidden;
                                            echo $hidden2;
                                            echo "Room Name : ".$activeRoom."<br>";
                                            echo "Title : ".$singleReservation['title']."<br>";
                                            echo "Date : ".$startDateTime[0]."<br>";
                                            echo "Start Time : ".$startDateTime[1]."<br>";
                                            echo "End Time : ".$endDateTime[1];
                                    } else {
                                        echo "<section class = 'leftcolumn'>";
                                            echo $hidden;
                                            echo $hidden2;
                                            echo "Room Name : ".$activeRoom."<br>";
                                            echo "Title : ".$singleReservation['title']."<br>";
                                            echo "Date : ".$startDateTime[0]."<br>";
                                            echo "Start Time : ".$startDateTime[1]."<br>";
                                            echo "End Time : ".$endDateTime[1];
                                    }

                                        echo "</section>";
                                        echo "<aside class = 'rightcolumn'>";
                                            echo $deleteButton."<br>";
                                            echo $modifyButton."<br><br>";
                                        echo "</aside>";
                                    echo "</form>";
                                    $count = $count + 1;
                                }
                            }

                            $db->closeServerConn($conn);*/
                            ?>
                        </div><!-- End modal-body -->
                    </div><!-- End modal content -->
                </div><!-- End modal-dialog -->
            </div><!-- End MyReservations Modal -->
            <div id="reservation-table"><br></div>
            <!-- id reservation-table -->
        </div>
        <!-- Class row -->
    </div>
    <!-- class containter -->
</div>
<!-- class="intro-header" -->


<!-- Conflict Resolution Message -->

<div id="conflictResolutionMessage" style="display: none;" title="Conflict Resolution">

</div>


<!-- Reservation Creation Container -->
<div id="reservationContainerMessage" style="display: none;" title="Create Reservation">

</div>


<!-- My Reservations Modal -->
<div id="myReservationsModal" style="display: none;">
    <table class="table" id="reservationsTable" width="100%">
        <thead>
        <tr>
            <th>Reservation ID</th>
            <th>Name</th>
            <th>Room ID</th>
            <th>Room Name</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Action</th>

        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>


</body>

</html>

