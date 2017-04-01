<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php';
Stark\WebUser::isLoggedIn(TRUE);
use Stark\WebUser;

$RoomDirectory = new \Stark\RoomDirectory();
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
    <link href="../../vendor/components/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Custom CSS -->
    <link href="../../CSS/landing-page-Registration.css" rel="stylesheet">
    <!-- jQuery UI CSS -->
    <link href="../../vendor/components/jqueryui/themes/hot-sneaks/jquery-ui.min.css" rel="stylesheet" type="text/css">

    <!-- DataTables CSS -->
    <link href="../../vendor/datatables/datatables/media/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- DataTables Buttons Extension -->
    <link href="../../plugins/datatables/extensions/Buttons/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="../../plugins/datatables/extensions/Buttons/css/buttons.bootstrap.min.css" rel="stylesheet">
    <!-- DataTable Select Extension -->
    <link href="../../plugins/datatables/extensions/Select/css/select.dataTables.min.css" rel="stylesheet">
    <!-- FUll calendar CSS-->
    <link href='../../plugins/fullcalendar/fullcalendar.css' rel='stylesheet'/>
    <link href='../../plugins/fullcalendar/scheduler.min.css' rel="stylesheet">
    <link href='../../plugins/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print'/>


    <!-- jQuery -->
    <script src="../../vendor/components/jquery/jquery.min.js"></script>
    <!-- jQuery UI -->
    <script src="../../vendor/components/jqueryui/jquery-ui.min.js"></script>
    <!-- bootstrap js -->
    <script src="../../vendor/components/bootstrap/js/bootstrap.min.js"></script>
    <!-- DataTables JavaScript -->
    <script src="../../vendor/datatables/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables/datatables/media/js/dataTables.bootstrap4.min.js"></script>
    <!-- DataTable extensions -->
    <script src="../../plugins/datatables/extensions/Buttons/js/dataTables.buttons.js"></script>
    <script src="../../plugins/datatables/extensions/Buttons/js/buttons.bootstrap.min.js"></script>
    <script src="../../plugins/datatables/extensions/Select/js/dataTables.select.min.js"></script>
    <script src="../../plugins/datatables/extensions/Buttons/js/buttons.flash.js"></script>
    <!-- Moment -->
    <script src='../../plugins/fullcalendar/moment.min.js'></script>
    <!-- FullCalendar Io -->
    <script src='../../plugins/fullcalendar/fullcalendar.min.js'></script>
    <script src='../../plugins/fullcalendar/scheduler.js'></script>
    <script src="../../plugins/jquerysession/session.js"></script>
    <script src="../../js/calendar.js"></script>


    <script>

        //todo: needs some refactoring
        USER_ID = '<?php echo WebUser::getUser()->getUserId()?>';
        ;
        //todo: needs some refactoring (yeah no kidding...)

        CCOUNT = "<?php echo \Stark\CoreConfig::settings()['reservations']['lock']; ?>";

        var t, count;

        function cddisplay() {
            // displays time in span
            console.log(count);
            $('#timer').html(count);
        }
        ;

        function countdown() {
            // starts countdown
            cddisplay();
            if (count == 0) {
                $('#myModal').dialog("close");
            }
            else {
                count--;
                t = setTimeout("countdown()", 1000);
            }
        }
        ;

        function cdpause() {
            // pauses countdown
            clearTimeout(t);
        }
        ;

        function cdreset() {
            // resets countdown
            cdpause();
            count = CCOUNT;
            cddisplay();
        }
        ;

        function openReservation(roomid) {
            // open modal
            $('#myModal').dialog({
                width: 1200,
                modal: true,
                height: 700,
                show: 'fade',
                title: 'Make Reservation',
                buttons: {
                    "Close": function () {
                        $(this).dialog('destroy');
                    }
                },
                Close: function () {
                    // erase any error messages that might have been created
                    $('#resultsReservation').html("");
                },
                beforeClose: function (event, ui) {
                    //unlock room
                    $.ajax({
                        type: "POST",
                        url: "Lock.php", //file name
                        dataType: "json",
                        data: {
                            action: "unlock",
                            roomID: roomid
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

                    $(this).dialog("destroy");
                }
            });
        }

        $(function () {
            // initialize booking tabs
            $("#tabs").tabs();


            // add the date picker
            $('input#rDate').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 0
            });


            // bind accordion to equipment
            $("#accordionEquipment").accordion({
                heightStyle: "content"
            });

            //what happens when you click on the make reserve button
            $(document).on('click', '#makeReserve', function () {
                // count errors to check if we can actually open modal for reservation
                errors = 0;

                var roomid = $('#roomOptions').val();
                var roomName = $('#roomOptions option[value=' + roomid + ']').text();
                $('#roomName').val(roomName);
                $('#roomID').val(roomid);

                $('#lockMessageModal').dialog({

                    width: 300,
                    height: 200,
                    modal: true

                });
                $('#lockMessage').html("Please wait...");

                // lock room
                $.ajax({
                    type: "POST",
                    url: "Lock.php", //file name
                    dataType: "json",
                    data: {
                        action: "lock",
                        roomID: roomid
                    },
                    success: function (data) {
                        // start countdown

                        console.log(data);
                        if (data.success) {
                            if (data.remaining !== undefined) {
                                //let the timer start where it left off for the user
                                CCOUNT = data.remaining;
                            }
                            $('#lockMessageModal').dialog('close');
                            openReservation(roomid);
                            cdreset();
                            countdown();
                        }
                        else {

                            $('#lockMessage').html(data.error);
                        }
                    }
                });

            });


            // cancel reservation
            $(document).on('click', '#cancelReservation', function () {

                var reservation = userReservations.row($(this).closest('tr')).data();

                $('#cancelReservationModal').dialog({
                    title: "Cancel Reservation",
                    modal: true,
                    buttons: {
                        "Yes": function () {
                            $(this).dialog('close');
                            $('#cancelMessage').html("Canceling reservation...please wait...").dialog({
                                modal: true
                            });

                            $.ajax({
                                url: 'Ajax/delete.php',
                                data: {
                                    reid: reservation.reid
                                },
                                error: function () {
                                    alert('An error occurred');
                                },
                                success: function (data) {
                                    $('#cancelMessage').html(data);
                                }
                            });
                        },
                        "No": function () {
                            $(this).dialog("destroy");
                        }
                    }
                });
            });

            // Modify reservation
            $(document).on('click', '#modifyReservation', function () {

                // add the date picker
                $('input#newDate').datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: 0
                });

                var reservation = userReservations.row($(this).closest('tr')).data();
                var reservationId = reservation.reid;
                // Capture any equipment selected
                var equipment = [];
                // Capture any change in equipment
                var changedEquipment = false;

                var roomId = reservation.rid;
                $('#newRoomOptions').val(roomId.toString());

                $('input#newDate').val(reservation.Date);
                $('input#newStartTime').val(reservation.StartTime);
                $('input#newEndTime').val(reservation.EndTime);
                $('input#newTitle').val(reservation.title);

                $('#modifyReservationModal').dialog({
                    width: 600,
                    title: "Modify Reservation",
                    modal: true,
                    buttons: {
                        "Submit": function () {

                            $('#modifyMessage').html("Modifying reservation...please wait...").dialog({
                                modal: true
                            });

                            var newDate = $('input#newDate').val();
                            var newStartTime = $('input#newStartTime').val();
                            var newEndTime = $('input#newEndTime').val();
                            var newTitle = $('input#newTitle').val();
                            var newRoomId = $('#newRoomOptions').val();

                            $.ajax({
                                url: 'Ajax/Modify.php',
                                data: {
                                    reservationId: reservationId,
                                    date: newDate,
                                    startTime: newStartTime,
                                    endTime: newEndTime,
                                    title: newTitle,
                                    roomId: newRoomId,
                                    equipment: JSON.stringify(equipment),
                                    changedEquipment: changedEquipment
                                },
                                error: function () {
                                    alert('An error occurred');
                                },
                                success: function (data) {
                                    $('#modifyMessage').html(data);
                                }
                            });
                        },
                        "Change Equipment": function () {

                            // list of database equipment
                            var computersListTableModify = $('#computersListTableModify').DataTable({
                                "processing": true,
                                "destroy": true,
                                "serverSide": false,
                                "select": true,
                                "displayLength": 25,
                                "ajax": {
                                    "url": 'Ajax/computerList.php',
                                    "type": "POST",
                                },
                                "columns": [
                                    {"data": "EquipmentId"},
                                    {"data": "Manufacturer"},
                                    {"data": "ProductLine"},
                                    {"data": "Description"},
                                    {"data": "Cpu"},
                                    {"data": "Ram"}
                                ],
                                'order': [[0, "asc"]],
                                columnDefs: [{
                                    orderable: false,
                                    targets: [5]
                                }],
                            });

                            var projectorsListTableModify = $('#projectorsListTableModify').DataTable({
                                "processing": true,
                                "destroy": true,
                                "serverSide": false,
                                "select": true,
                                "displayLength": 25,
                                "ajax": {
                                    "url": 'Ajax/projectorList.php',
                                    "type": "POST",
                                },
                                "columns": [
                                    {"data": "EquipmentId"},
                                    {"data": "Manufacturer"},
                                    {"data": "ProductLine"},
                                    {"data": "Description"},
                                    {"data": "Display"},
                                    {"data": "Resolution"}
                                ],
                                'order': [[0, "asc"]],
                                columnDefs: [{
                                    orderable: false,
                                    targets: [5]
                                }],
                            });

                            $('#modifyEquipmentModal').dialog({
                                title: "Modify Equipment",
                                modal: true,
                                width: 1200,
                                height: 500,
                                buttons: {
                                    "Save Changes": function () {

                                        equipment = [];
                                        changedEquipment = true;
                                        var computerAlt = $('#computerAltModify').is(':checked');
                                        var projectorAlt = $('#projectorAltModify').is(':checked');

                                        projectorsListTableModify.rows('.selected').every(function (rowIdx, tableLoop, rowLoop) {
                                            var data = this.data();
                                            equipment.push([data.EquipmentId, 'c', computerAlt]);
                                        });

                                        computersListTableModify.rows('.selected').every(function (rowIdx, tableLoop, rowLoop) {
                                            var data = this.data();
                                            equipment.push([data.EquipmentId, 'p', projectorAlt]);
                                        });

                                        $(this).dialog("destroy");
                                    },
                                    "Cancel": function () {
                                        $(this).dialog("destroy");
                                    }
                                }
                            });
                        },
                        "Cancel": function () {
                            $(this).dialog("destroy");
                        }
                    }
                });
            });

            //when clicking on View for a user's equipment if he has for his/her reservation

            $(document).on('click', '#viewEquipment', function () {
                $row = $(this).closest('tr');
                var reservation = userReservations.row($row).data();
                var reservationId = reservation.reid;

                if (!reservationId) {
                    return;
                }


                $('#myEquipmentModal').dialog({
                    width: 1000,
                    height: 500,
                    title: 'Loaned Equipment for Reservation #' + reservationId
                });

                // get the reservation's project list
                myProjectorsListTable = $('#myProjectorsListTable').DataTable({
                    "processing": true,
                    "destroy": true,
                    "serverSide": false,
                    "displayLength": 5,
                    "ajax": {
                        "url": 'Ajax/myProjectorList.php',
                        "type": "POST",
                        "data": {
                            id: reservationId
                        }
                    },
                    "columns": [
                        {"data": "EquipmentId"},
                        {"data": "Manufacturer"},
                        {"data": "ProductLine"},
                        {"data": "Description"},
                        {"data": "Display"},
                        {"data": "Resolution"}
                    ],
                    'order': [[0, "asc"]],
                    columnDefs: [{
                        orderable: false,
                        targets: [5]
                    }],
                });

                // get the reservation's computer list
                myComputersListTable = $('#myComputersListTable').DataTable({
                    "processing": true,
                    "destroy": true,
                    "serverSide": false,
                    "displayLength": 5,
                    "ajax": {
                        "url": 'Ajax/myComputerList.php',
                        "type": "POST",
                        "data": {
                            id: reservationId
                        }
                    },
                    "columns": [
                        {"data": "EquipmentId"},
                        {"data": "Manufacturer"},
                        {"data": "ProductLine"},
                        {"data": "Description"},
                        {"data": "Ram"},
                        {"data": "Cpu"}
                    ],
                    'order': [[0, "asc"]],
                    columnDefs: [{
                        orderable: false,
                        targets: [5]
                    }],
                });

            });

            // when clicking on profile link
            $(document).on('click', '#second-r', function () {

                $('#profilemyModal').dialog({
                    width: 800,
                    modal: true,
                    title: 'My Profile'
                });
            });

            // when click on the My Reservations Button
            $(document).on('click', '#third-r', function () {

                $('#myReservationsModal').dialog({
                    width: 1000,
                    height: 800,
                    modal: true,
                    title: 'My Reservations'
                });

            });

            // when saving profile information
            $(document).on('click', '#submitProfile', function () {

                $clicker = $(this);
                var originalText = $clicker.text();
                $clicker.text('Updating...');
                $clicker.addClass('disabled');
                var ser = $('form#profileForm').serialize();

                $('#resultsProfile').html("");

                console.log(ser);

                $.ajax({
                    type: "POST",
                    url: "UpdateProfile.php", //file name
                    data: ser,
                    success: function (data) {
                        $clicker.text(originalText);
                        $('#resultsProfile').html(data);
                    },
                    complete: function () {
                        $clicker.text(originalText);
                        $clicker.removeClass('disabled');
                    },
                    error: function () {
                        $clicker.text(originalText);
                    }
                });
            });

            // when click on the big reserve button to create  a reservation
            $(document).on('click', '#createReservation', function () {

                var computerAlt = $('#computerAltReserve').is(':checked');
                var projectorAlt = $('#projectorAltReserve').is(':checked');

                // capture any equipment selected
                var equipment = [];

                computersListTable.rows('.selected').every(function (rowIdx, tableLoop, rowLoop) {
                    var data = this.data();
                    equipment.push([data.EquipmentId, 'c', computerAlt]);
                });

                projectorsListTable.rows('.selected').every(function (rowIdx, tableLoop, rowLoop) {
                    var data = this.data();
                    equipment.push([data.EquipmentId, 'p', projectorAlt]);
                });


                /// capture form
                $form = $('#reservationForm');
                $clicker = $(this);
                var originalText = $clicker.text();
                $clicker.text('Submitting...');
                $clicker.addClass('disabled');
                var ser = $form.serialize();

                $('#resultsReservation').html("");

                console.log(ser);
                console.log(equipment);

                $.ajax({
                    type: "POST",
                    url: "Ajax/Reserve.php", //file name
                    data: {
                        formData: ser,
                        equipment: JSON.stringify(equipment)
                    },
                    success: function (data) {
                        $clicker.text(originalText);
                        $('#resultsReservation').html(data);
                    },
                    complete: function () {
                        $clicker.text(originalText);
                        $clicker.removeClass('disabled');
                    },
                    error: function () {
                        $clicker.text(originalText);
                    }
                });

            });


            // list of static computeres
            computersListTable = $('#computersListTable').DataTable({
                "processing": true,
                "destroy": true,
                "serverSide": false,
                "select": true,
                "displayLength": 25,
                "ajax": {
                    "url": 'Ajax/computerList.php',
                    "type": "POST",
                },
                "columns": [
                    {"data": "EquipmentId"},
                    {"data": "Manufacturer"},
                    {"data": "ProductLine"},
                    {"data": "Description"},
                    {"data": "Cpu"},
                    {"data": "Ram"}
                ],
                'order': [[0, "asc"]],
                columnDefs: [{
                    orderable: false,
                    targets: [5]
                }],
            });

            // list of static projects
            projectorsListTable = $('#projectorsListTable').DataTable({
                "processing": true,
                "destroy": true,
                "serverSide": false,
                "select": true,
                "displayLength": 25,
                "ajax": {
                    "url": 'Ajax/projectorList.php',
                    "type": "POST",
                },
                "columns": [
                    {"data": "EquipmentId"},
                    {"data": "Manufacturer"},
                    {"data": "ProductLine"},
                    {"data": "Description"},
                    {"data": "Display"},
                    {"data": "Resolution"}
                ],
                'order': [[0, "asc"]],
                columnDefs: [{
                    orderable: false,
                    targets: [5]
                }],
            });


            // variable that holds the user's reservations
            userReservations = $('#reservationsTable').DataTable({
                "processing": true,
                "destroy": true,
                "serverSide": false,
                "displayLength": 25,
                "ajax": {
                    "url": 'Ajax/StudentReservations.php',
                    "type": "POST",
                },
                "columns": [
                    {"data": "reid"},
                    {"data": "title"},
                    {"data": "roomName"},
                    {"data": "Date"},
                    {"data": "StartTime"},
                    {"data": "EndTime"},
                    {

                        'render': function (data, type, row) {
                            if (row.Waiting) {
                                return 'Waiting';
                            }
                            else {
                                return "<span class='confirmed'>Confirmed</span>";
                            }
                        },
                        className: "dt-center"
                    },
                    {"data": "WaitListPosition"},
                    {
                        //hasEquipment
                        'render': function (data, type, row) {
                            if (row.hasEquipment) {
                                return '<button id="viewEquipment" name="viewEquipment" type="button" class="btn btn-outline btn-primary btn-square btn-sm">View</button>';
                            }
                            else {
                                return "--";
                            }
                        },
                        className: "dt-center"

                    },
                    {
                        'render': function (data, type, row) {
                            if (row.canModify) {
                                return '<button id="modifyReservation" name="modifyReservation" type="button" class="btn btn-primary">Modify</button>' +
                                    ' <button id="cancelReservation" name="cancelReservation" type="button" class="btn btn-danger">Cancel</button>';
                            }
                            else {
                                return "-";
                            }
                        },
                        className: "dt-center"
                    }
                ],
                'order': [[0, "asc"]],
                columnDefs: [{
                    orderable: false,
                    targets: [5]
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
                            foreach ($RoomDirectory->getRooms() as $RoomDomain) {
                                ?>
                                <option value="<?php echo $RoomDomain->getRoomId(); ?>"><?php echo $RoomDomain->getName(); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <!-- Hidden input for temporary datepicker fix-->
                        <input type="hidden" readonly="readonly" type="text" class="form-control" name="dateDrop"
                               id="dateDrop" placeholder="Nothing"/>
                        <button type="button" class="btn btn-default btn-lg" id="makeReserve"><span
                                    class="network-name">Make a Reservation</span></button>

                    </div>
                    <br>

                </div>
                <br>
            </div>
            <!-- Calendar -->
            <div id="calendar"></div>

            <!-- Reservation Modal -->
            <div id="myModal" role="dialog" style="display: none;">
                <div>
                    <!-- Modal content-->
                    <div>

                        <form id="reservationForm">

                            <div class="timer" style="color:red;text-align: center;">Reservation closes in <span
                                        id="timer">-</span> seconds!
                            </div>
                            <div class="modal-body">

                                <div id="tabs">

                                    <ul>
                                        <li><a href="#tabs-1">Booking</a></li>
                                        <li><a href="#tabs-2">Equipment</a></li>

                                    </ul>

                                    <div id="tabs-1" class="form-group">
                                        <label for="roomName">Room:</label>
                                        <input readonly="readonly" class="form-control" id="roomName" name="roomName"/>
                                        <label>Title of Reservation</label>
                                        <input required type="text" class="form-control" placeholder="Enter a Title"
                                               name="title">
                                        <label>Description of Reservation</label>
                                        <textarea style="resize:none;" rows="3" cols="50"
                                                  placeholder="Describe the Reservation here..." class="form-control"
                                                  name="description"></textarea>
                                        <label for="rDate">Date:</label>
                                        <input type="text" class="form-control" name="rDate" id="rDate"/> <br>
                                        <label for="startTime">Start Time: (hh:mm)</label>
                                        <input type="text" class="form-control" id="startTime" name="startTime">
                                        <label for="endTime">End Time: (hh:mm)</label>
                                        <input type="text" class="form-control" id="endTime" name="endTime">
                                        <input hidden name="roomID" id="roomID">
                                        <br>
                                        <label for="repeatReservation">Repeat Reservation for:</label>
                                        <select id="repeatReservation" name="repeatReservation">
                                            <option value="1">1 Week</option>
                                            <option value="2">2 Weeks</option>
                                            <option value="3">3 Weeks</option>
                                        </select>
                                    </div>
                                    <div id="tabs-2">
                                        <div class="text-center h1">Computers</div>
                                        <label for="computerAltReserve">Allow alternative computers to be
                                            assigned</label>
                                        <input id="computerAltReserve" type="checkbox" checked="checked"/>
                                        <table id="computersListTable" width="100%" class="table table-responsive">
                                            <thead>
                                            <tr>
                                                <th>Equipment ID</th>
                                                <th>Manufacturer</th>
                                                <th>Product Line</th>
                                                <th>Description</th>
                                                <th>CPU</th>
                                                <th>RAM</th>
                                            </tr>
                                            </thead>
                                        </table>
                                        <div class="text-center h1">Projectors</div>
                                        <label for="projectorAltReserve">Allow alternative projectors to be
                                            assigned</label>
                                        <input id="projectorAltReserve" type="checkbox" checked="checked"/>
                                        <table id="projectorsListTable" width="100%" class="table table-responsive">
                                            <thead>
                                            <tr>
                                                <th>Equipment ID</th>
                                                <th>Manufacturer</th>
                                                <th>Product Line</th>
                                                <th>Description</th>
                                                <th>Display</th>
                                                <th>Resolution</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <button type="button" id="createReservation"
                                        class="btn btn-default btn-success btn-block">Create Reservation
                                </button>
                            </div>
                        </form>
                        <div id="resultsReservation"></div>
                    </div>
                </div>
            </div>

            <!-- Edit Reservation Modal -->

            <!-- Profile Modal -->
            <div id="profilemyModal" style="display: none;">
                <div>
                    <!-- Modal content-->
                    <div>
                        <div class="modal-body">
                            <form id="profileForm">
                                <div class="form-group">
                                    <label>First name</label>
                                    <input readonly="readonly" type="text" class="form-control" name="firstName"
                                           id="firstName"
                                           placeholder="First Name"
                                           value="<?php echo WebUser::getUser()->getFirstName(); ?>"/>
                                </div>
                                <div class="form-group">
                                    <label>Last name</label>
                                    <input readonly="readonly" type="text" class="form-control" name="lastName"
                                           id="lastName"
                                           placeholder="Last Name"
                                           value="<?php echo WebUser::getUser()->getLastName(); ?>"/>
                                </div>
                                <div class="form-group">
                                    <label>Student ID</label>
                                    <input readonly="readonly" type="text" class="form-control" name="studentID"
                                           placeholder="Student ID"
                                           value="<?php echo WebUser::getUser()->getStudentId(); ?>"/>
                                </div>

                                <div class="form-group">
                                    <label>Old Password</label>
                                    <input type="password" class="form-control" name="oldPass"
                                           placeholder="Old Password"/>
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="newPass"
                                           placeholder="New Password"/>
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="text" class="form-control" name="uEmail" id="uEmail"
                                           placeholder="Email Address"
                                           value="<?php echo WebUser::getUser()->getUserName(); ?>"/>
                                </div>
                                <button type="button" id="submitProfile" class="btn btn-default btn-success btn-block">
                                    Submit
                                </button>
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

<!-- Conflict Resolution Message -->

<div id="conflictResolutionMessage" style="display: none;" title="Conflict Resolution"></div>
<!-- Reservation Creation Container -->
<div id="reservationContainerMessage" style="display: none;" title="Create Reservation"></div>

<!-- My Reservations Modal -->
<div id="myReservationsModal" style="display: none;">
    <table class="table" id="reservationsTable" width="100%">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Room Name</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Status</th>
            <th>Waitlist Position</th>
            <th>Equipment</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>


<!-- My Reservation Equipment -->
<div id="myEquipmentModal" style="display: none;">
    <div id="accordionEquipment">
        <h3>Computers</h3>
        <div>
            <table id="myComputersListTable" width="100%" class="table table-bordered">
                <thead>
                <tr>
                    <th>Equipment ID</th>
                    <th>Manufacturer</th>
                    <th>Product Line</th>
                    <th>Description</th>
                    <th>CPU</th>
                    <th>RAM</th>
                </tr>
                </thead>
            </table>
        </div>
        <h3>Projectors</h3>
        <div>
            <table id="myProjectorsListTable" width="100%" class="table table-bordered">
                <thead>
                <tr>
                    <th>Equipment ID</th>
                    <th>Manufacturer</th>
                    <th>Product Line</th>
                    <th>Description</th>
                    <th>Display</th>
                    <th>Resolution</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Cancel Reservation -->
<div id="cancelReservationModal" style="display: none;" title="Delete Reservation?">
    <p>
        <span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>You are about to delete
        this reservation. Are you sure?
    </p>
</div>

<!-- Modify Reservation -->
<div id="modifyReservationModal" style="display: none;" title="Modify Reservation">
    <label for="newTitle">Title of Reservation</label>
    <input required type="text" class="form-control" placeholder="Enter a Title"
           name="title" id="newTitle">
    <div style="margin-top: 10px;">
        <label for="newRoomOptions">New Room:</label>
        <select id="newRoomOptions" class="btn btn-default btn-lg network-name" name="newRoomOptions">
            <?php
            /**
             * @var \Stark\Models\Room $RoomDomain
             */
            foreach ($RoomDirectory->getRooms() as $RoomDomain) {
                ?>
                <option value="<?php echo $RoomDomain->getRoomId(); ?>"><?php echo $RoomDomain->getName(); ?></option>
                <?php
            }
            ?>
        </select>
    </div>
    <label for="newDate">New Date:</label>
    <input type="text" class="form-control" name="newDate" id="newDate"/> <br>
    <label for="newStartTime">New Start Time: (hh:mm)</label>
    <input type="text" class="form-control" id="newStartTime" name="newStartTime">
    <label for="newEndTime">New End Time: (hh:mm)</label>
    <input type="text" class="form-control" id="newEndTime" name="newEndTime">
</div>

<!-- Modify Equipment -->
<div id="modifyEquipmentModal" style="display: none;" title="Modify Equipment">
    <div class="text-center h1">Computers</div>
    <label for="computerAltModify">Allow alternative computers to be assigned</label>
    <input id="computerAltModify" type="checkbox" checked="checked"/>
    <table id="computersListTableModify" width="100%" class="table table-responsive">
        <thead>
        <tr>
            <th>Equipment ID</th>
            <th>Manufacturer</th>
            <th>Product Line</th>
            <th>Description</th>
            <th>CPU</th>
            <th>RAM</th>
        </tr>
        </thead>
    </table>
    <div class="text-center h1">Projectors</div>
    <label for="projectorAltModify">Allow alternative projectors to be assigned</label>
    <input id="projectorAltModify" type="checkbox" checked="checked"/>
    <table id="projectorsListTableModify" width="100%" class="table table-responsive">
        <thead>
        <tr>
            <th>Equipment ID</th>
            <th>Manufacturer</th>
            <th>Product Line</th>
            <th>Description</th>
            <th>Display</th>
            <th>Resolution</th>
        </tr>
        </thead>
    </table>
</div>

<!-- Lock Message -->
<div id="lockMessageModal" style="display: none;" title="Reservation">
    <p>
        <span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span><span
                id="lockMessage"></span>
    </p>
</div>

<!-- Reservation Cancel Message -->
<div id="cancelMessage" style="display: none;" title="Cancel Reservation">

    <!-- Reservation Modify Message -->
    <div id="modifyMessage" style="display: none;" title="Modify Reservation">

    </div>

</body>

</html>
