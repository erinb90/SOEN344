<!DOCTYPE html>
<html lang="en">

<!--
	September 27, 2016 (Joey)
	-Added jQuery 1.x, in order for datepicker to work (try to upgrade to 3.1.1)
	-Added datepicker to basic img
	
	September 30, 2016 (Stefano)
	-Added Modal to the Reservation page when users selects make reservation button
	-Made table entities clickable to activate reservations

	October 1, 2016 (Joey)
	-Added calender as default, no image required
	-Added auto-generation of date in reservation table, based on user click of the calender
	
	!-Still necessary to pass entity ID of time selected
	!-Colors are not permanent, was done to check if CSS worked for table
	!-Red should indicate times that are booked
	!-Grey should indicate times that are not booked
-->

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

	<!-- jQuery -->
    <script src="../../Javascript/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../../Javascript/bootstrap.min.js"></script>
	
	<!--jQuery stuff-->
	<!--Try to update to new jquery, doesn't seem to work with jquery 3.1.1-->
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script type="text/javascript">

		$(document).ready(function() {
			var monthNames = ["January", "February", "March", "April", "May", "June",
				"July", "August", "September", "October", "November", "December"];
			var dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
			var currentDate = new Date();
			
			/******
			 * This date generation should be made into a function, including the arrays above,
			 * and would accept a Date object
			 */
			todayDate = dayNames[currentDate.getDay()] + ", " 
								+ monthNames[currentDate.getMonth()] + " " 
								+ currentDate.getDate() + " "
								+ currentDate.getUTCFullYear();

			document.getElementById("datetoday").innerHTML = todayDate;
			/** end of the function, resued in "onSelect" feature of datepicker */
			
    		$("#datepickerInline").datepicker({
        	//	buttonImage: '../img/calendar.png',
        	//	buttonImageOnly: true,
        		changeMonth: true,
        		changeYear: true,
        		showOn: 'both',
				onSelect: function(event) {
					var pickedDate = $("#datepickerInline").datepicker("getDate");
					todayDate = dayNames[pickedDate.getDay()] + ", " 
								+ monthNames[pickedDate.getMonth()] + " " 
								+ pickedDate.getDate() + " "
								+ pickedDate.getUTCFullYear();
					document.getElementById("datetoday").innerHTML = todayDate;
				}
     		});
  		});
	</script>

	<!-- Pop-up for reservation (by clicking Button)
	Will have to include a way of indicating that start time must be 8:00am
	-->
	<script>
		$(document).ready(function(){
			$("#myBtn").click(function(){
				$("#myModal").modal();
			});
		});
	</script>

	<br><br>

	<!-- Pop-up for reservation (by clicking table)
	Will have to include some way of passing the time from the block chosen to popup
	-->
	<script>
		$(document).ready(function(){
			$(".slot").click(function(){
				$("#myModal").modal();
			});
		});
	</script>

	<!-- Pop-up for profile editing (by clicking My Profile)-->
	<script>
		$(document).ready(function(){
			$("#second-r").click(function(){
				$("#profilemyModal").modal();
			});
		});
	</script>
		
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top topnav">
        <div class="container topnav">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                
				<a class="navbar-brand topnav first r" id="first-r" href="../../index.php">Log Out</a>
				<a class="navbar-brand topnav second r" id="second-r" href="#">My Profile</a>
				<a class="navbar-brand topnav third r" id="third-r" href="#">My Reservations</a>
				<a class="navbar-brand topnav fourth r" id="fourth-r" href="https://my.concordia.ca/psp/upprpr9/EMPLOYEE/EMPL/h/?tab=CU_MY_FRONT_PAGE2">MyConcordia</a>
        
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Header -->
	<div class="intro-header">
		<div class="container">
			<div class="row">
				<div class="greeting">
					<h1>Please select a Day to Begin</h1>
				</div>
				<!-- class greeting -->

				<br><br>

				<!-- Div for datepicker -->
				<div id="datepickerContainer">
					<div id="datepickerInline"></div>
					<br><br>
					<div id="reserveButton">
						<a class="btn btn-default btn-lg" data-target="myModal" id="myBtn"><span class="network-name">Make a Reservation</span></a>
					</div>
				</div>

				<br><br>

				<!-- Reservation Modal -->
				<div class="modal fade" id="myModal" role="dialog">
					<div class="modal-dialog">
						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 style="color:red;">Please enter the specifications for your Reservation</h4>
							</div>
							<div class="modal-body">
								<form id="form">

									<div class="form-group">
										<label>Title of Reservation</label>
										<input type="text" class="form-control" id="title" placeholder="Enter a Title">
									</div>
									<div class="form-group">
										<label>Description of Reservation</label>
										<input textarea rows="4" cols="50" class="form-control" id="description" placeholder="Enter your Description">
									</div>

									<!-- Time slots should be inserted here-->


									<!-- Should be Auto-Populated and Non-Editable-->
									<div class="form-group">
										<label>First Name</label>
										<input disabled type="text" class="form-control" id="firstname" placeholder="Auto-Populated Name">
									</div>
									<div class="form-group">
										<label>Last Name</label>
										<input disabled type="text" class="form-control" id="lastname" placeholder="Auto-Populated Name">
									</div>
									<div class="form-group">
										<label>Student ID</label>
										<input disabled type="text" class="form-control" id="studentID" placeholder="Auto-Populated ID">
									</div>
									<div class="form-group">
										<label>Program</label>
										<input disabled type="text" class="form-control" id="program" placeholder="Auto-Populated Program">
									</div>
									<div class="form-group">
										<label>Email Address</label>
										<input disabled type="text" class="form-control" id="email" placeholder="Auto-Populated Email">
									</div>

									<!-- Requires Back-end connection -->
									<button type="submit" class="btn btn-default btn-success btn-block">Submit</button>

								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- Profile Modal -->
				<div class="modal fade" id="profilemyModal" role="dialog">
					<div class="modal-dialog">
						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 style="color:red;">Edit any of your profile info!</h4>
							</div>
							<div class="modal-body">
								<form id="form">
									<div class="form-group">
										<label>First Name</label>
										<input type="text" class="form-control" id="firstname" placeholder="First Name">
									</div>
									<div class="form-group">
										<label>Last Name</label>
										<input type="text" class="form-control" id="lastname" placeholder="Last Name">
									</div>
									<div class="form-group">
										<label>Student ID</label>
										<input type="text" class="form-control" id="studentID" placeholder="Student ID">
									</div>
									<div class="form-group">
										<label>Program</label>
										<input type="text" class="form-control" id="program" placeholder="Program">
									</div>
									<div class="form-group">
										<label>Old Password</label>
										<input type="text" class="form-control" id="program" placeholder="Old Password">
									</div>
									<div class="form-group">
										<label>New Password</label>
										<input type="text" class="form-control" id="program" placeholder="New Password">
									</div>
									<div class="form-group">
										<label>Email Address</label>
										<input type="text" class="form-control" id="email" placeholder="Email Address">
									</div>

									<!-- Requires Back-end connection -->
									<button type="submit" class="btn btn-default btn-success btn-block">Submit</button>

								</form>
							</div>
						</div>
					</div>
				</div>

				<div id="reservation-table"><br>
					<table class="reservations" border="1" cellpadding="0" width="100%">
						<tbody>
							<tr class="today">
								<td class="date" id="datetoday"></td>
								<td class="time" colspan="2">00:00</td>
								<td class="time" colspan="2">01:00</td>
								<td class="time" colspan="2">02:00</td>
								<td class="time" colspan="2">03:00</td>
								<td class="time" colspan="2">04:00</td>
								<td class="time" colspan="2">05:00</td>
								<td class="time" colspan="2">06:00</td>
								<td class="time" colspan="2">07:00</td>
								<td class="time" colspan="2">08:00</td>
								<td class="time" colspan="2">09:00</td>
								<td class="time" colspan="2">10:00</td>
								<td class="time" colspan="2">11:00</td>
								<td class="time" colspan="2">12:00</td>
								<td class="time" colspan="2">13:00</td>
								<td class="time" colspan="2">14:00</td>
								<td class="time" colspan="2">15:00</td>
								<td class="time" colspan="2">16:00</td>
								<td class="time" colspan="2">17:00</td>
								<td class="time" colspan="2">18:00</td>
								<td class="time" colspan="2">19:00</td>
								<td class="time" colspan="2">20:00</td>
								<td class="time" colspan="2">21:00</td>
								<td class="time" colspan="2">22:00</td>
								<td class="time" colspan="2">23:00</td>
							</tr>

							<!-- Must be placed after element is created		
			<script>
				var tD = new Date();
				var datestr = (tD.getMonth()+ 1) + "/" + tD.getDate() + "/" + tD.getFullYear();
				document.getElementById("datetoday").innerHTML = datestr;
			</script>
			
			<script>
				var currentDate = $( ".selector" ).datepicker( "getDate" );
				document.getElementById("datetoday").innerHTML = currentDate;
			</script> -->
							<tr class="today">
								<td class="room" id="room1">Room1</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
							</tr>

							<tr class="today">
								<td class="room" id="room2">Room2</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
							</tr>

							<tr class="today">
								<td class="room" id="room3">Room3</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
							</tr>

							<tr class="today">
								<td class="room" id="room4">Room4</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
							</tr>

							<tr class="today">
								<td class="room" id="room5">Room5</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
								<td class="slot" colspan="1">0</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- id reservation-table -->
			</div>
			<!-- Class row -->
		</div>
		<!-- class containter -->
	</div>
	<!-- class="intro-header" -->

</body>

</html>