<!DOCTYPE html>
<?php

//Start session
session_start();

include "../Class/StudentMapper.php";
include "../Class/RoomMapper.php";
include "../Class/ReservationMapper.php";
include "../Class/WaitlistMapper.php";
include "../Class/RoomList.php";
include dirname(__FILE__)."/../Utilities/tableHelper.php";
include_once dirname(__FILE__).'/../Utilities/ServerConnection.php';

$db = new ServerConnection();
$conn = $db->getServerConn();

$email = $_SESSION['email'];
$userMSG = $_SESSION["userMSG"] ;
$msgClass = $_SESSION["msgClass"];
$modify = $_SESSION["modify"];
$made = $_SESSION['cleared'];
$roomAvailable = $_SESSION['roomAvailable'];
$passedDate = $_SESSION['selectedDate']; //Temporary fix for datepicker

$roomReserveID = $_SESSION['roomReserveID'];
$roomReserve;
if($roomReserveID != NULL) $roomReserve = new RoomMapper($roomReserveID, $conn);

if(isset($_SESSION["userMSG"])){
	unset($_SESSION['roomID']);
	unset($_SESSION['cleared']);
	unset($_SESSION["userMSG"]);
    unset($_SESSION["msgClass"]);
}

$student = new StudentMapper($email, $conn);
$reserve = new ReservationMapper();
$rooms = new RoomList($conn);

$firstName = $student->getFirstName();
$lastName = $student->getLastName();
$program = $student->getProgram();
$sID = $student->getSID();
$_SESSION["sID"] = $sID;
$test = $reserve->getREID($sID, $conn);
$studentReservations = $reserve->getReservations($student->getSID(), $conn);

//If user selects a reservation to modify, this will obtain the reservation details
$modReserve = array();
$modDate;
$modTimeEnd;

if($modify)
{
	//echo $_SESSION['reserveDate'];
	
	foreach($studentReservations as &$singleReservation)
	{ 
		if($_SESSION['reservation'] == $singleReservation['reservationID'])
		{
			$modReserve = $singleReservation;
		}
	}
	$modDate = explode(" ", $modReserve['startTimeDate']);
	$modTimeEnd = explode(" ", $modReserve['endTimeDate']);
	$roomReserve = new RoomMapper($modReserve['roomID'], $conn);
	$roomChosen = $roomReserve->getName();
}

$getStartHoursSelect = false;
$getEndHoursSelect = false;
//$non_studentRes = $reserve->getReservationsByDate("2016-11-11");
//var_dump($non_studentRes);

$today = date("d/m/Y");
$today = $reserve->getReservationsByDate($today, $conn);
function getHours($endTime = FALSE){
	global $getStartHoursSelect, $getEndHoursSelect;
	global $modDate, $modTimeEnd;
	if($endTime){
		$SOL = 1; //Start of list
	}else
		$SOL = 0;
	for($x = 0 + $SOL; $x < 48 + $SOL; $x++){
		
		$time = ((int)(($x%48)/2)) . ":";
		if($x % 2 == 1){
			$time .= "30";
		}else{
			$time .= "00";
		}
		$timeMod = $time;
		if(strlen($timeMod)==4)$timeMod = "0".$time;
		if($getStartHoursSelect && $timeMod == $modDate[1])
		{
			echo "<option selected='selected' value = '".$time."'>".$time."</option>";
		}
		elseif($getEndHoursSelect && $timeMod == $modTimeEnd[1])
		{
			echo "<option selected='selected' value = '".$time."'>".$time."</option>";
		}
		else
		{
			echo "<option value = '".$time."'>".$time."</option>";
		}
	}
}



$db->closeServerConn($conn);
?>

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

	October 9, 2016 (Joey)
	-Fixed connection to db for populating Make Reservation popup
	
	Novemeber 6, 2016 (Nick)
	-Added reservation tab functionality

	!-Still necessary to pass entity ID of time selected
	!-Colors are not permanent, was done to check if CSS worked for table
	!-Red should indicate times that are booked
	!-White should indicate times that are not booked
    !-Green should indicate times that are booked by user
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
	
	<!-- Table CSS -->
    <link href="../../CSS/Table.css" rel="stylesheet">

	<!-- jQuery -->
    <script src="../../Javascript/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../../Javascript/bootstrap.min.js"></script>
	
	<!--jQuery stuff-->
	<!--Try to update to new jquery, doesn't seem to work with jquery 3.1.1-->
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    
	<!-- All Javascript for Home.php page -->
	<script src="../../Javascript/Home.js"></script>
    
    <!-- Google Web Font Format for title -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Abel" rel="stylesheet">
        
<?php
    if($modify)
    {
		echo '<script> $(document).ready(function(){$("#editModal").modal("show");}); </script>';
		$_SESSION['modify'] = NULL; //Should clear out modify session if user refreshes
    }
	
	if($roomAvailable)
    {
		echo '<script> $(document).ready(function(){$("#myModal").modal("show");}); </script>';
    }
	
?>
</head>
<?php
	
	$regular = "<body>";
	$timer = '<body onload="lockoutSubmit('.$made.'),lockoutModify('.$made.'),lockoutDelete('.$made.')">';
	
	echo $timer;
	
?>
   
<!--<body onload="lockoutSubmit(document.getElementById('makeReserve'))"> -->
   <!-- Navigation -->
    <ul class="topnav" id="myTopnav">
		<li><a class="nav" href="../../index.php"><span style= "font-color:white">Log Out</span></a></li>
        <li><a class="nav" id = "second-r" href="#">My Profile</a></li>
        <li><a class="nav" id = "third-r" href="#">My Reservations</a></li>
        <li><a class="nav" href="https://my.concordia.ca/psp/upprpr9/EMPLOYEE/EMPL/h/?tab=CU_MY_FRONT_PAGE2">MyConcordia</a></li>
    </ul>

    <!-- Header -->
	<div class="intro-header">
	
		<div class="container">
			<div class="row">
				<!-- Id space to confirm if the data was saved or not -->
				<div>
					<?php
						if(!empty($userMSG)){
							echo '<div id = "details" class = "details '.$msgClass.'">'.$userMSG.'</div>';
						}
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
						<form id="form" action="CheckRoomAvailable.php" method="post">
							<div>
								<select id = "roomOptions" class="btn btn-default btn-lg network-name" name = "rID">
									<?php
										foreach($rooms->getRoomList() as $val){
											echo "<option value = '{$val[0]->getRID()}'>{$val[0]->getName()}</option>\n";
										}
									?>
								</select>
								<!-- Hidden input for temporary datepicker fix-->
								<input type="hidden" readonly="readonly" type="text" class="form-control" name = "dateDrop" id="dateDrop" placeholder = "Nothing" />
                                <button type="submit" class="btn btn-default btn-lg" id = "makeReserve"><span class="network-name">Make a Reservation</span></button>
								
							</div><br>
						</form>
					</div>
                    <br>
                    <div id="legendContainer">
                        <h6 class="legendTitle">LEGEND</h6>
                        <h6 class="green">Your Booking</h4>
                        <h6 class="red">Booked</h4>
                    </div>
				</div>

				<!-- Reservation Modal -->
				<div class="modal fade" id="myModal" role="dialog">
					<div class="modal-dialog">
						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4>Please enter the specifications for your reservation</h4>
							</div>
							<div class="modal-body">
								<form id="formRes" action="Reserve.php" method="post">
								
									<div class="form-group">
									<div class = "timer" style="color:red;text-align: center;">Reservation closes in <span id="timer"></span> seconds!</div>
										<label>Title of Reservation</label>
										<input required type="text" class="form-control" placeholder="Enter a Title"  name="title" >
									</div>
									<div class="form-group">
										<label>Description of Reservation</label>
										<textarea style="resize:none;" rows="3" cols="50" placeholder="Describe the Reservation here..." class="form-control" name="description"></textarea>
									</div>
									<!-- Time slots should be inserted here-->
									<div class="form-group">
										<label>Date:</label>
										<input readonly="readonly" type="text" class="form-control" name = "dateDrop" id="dateDrop" value="<?php echo $passedDate; ?>" />
										<br>										
										<label>Start Time:</label> 
											<select id ="startTime" name = "startTime">
												<?php getHours()?>
											</select>&nbsp &nbsp &nbsp
										<label>End Time:</label>
											<select id ="endTime" name = "endTime">
												<?php getHours( TRUE)?>
											</select>&nbsp &nbsp &nbsp
											<input readonly = "readonly" id = "roomOptionsMod" class="roomNum" name = "roomName" value="<?php if($roomReserve != NULL) echo $roomReserve->getName(); ?>"/>
											<input hidden name = "roomID" value="<?php if($roomReserve != NULL) echo $roomReserve->getRID(); ?>">
									</div>
									<div class="form-group">
										<label>Repeat Reservation for:</label>
										<select id="repeatReservation" name="repeatReservation">
											<option selected="selected">
											No Repeat
											</option>
											<option value="1">1 Week</option>
											<option value="2">2 Weeks</option>
											<option value="3">3 Weeks </option>
										</select>
									</div>
									<div class="form-group">
										<label>Name</label>
										<input readonly="readonly" type="text" class="form-control" name="firstName" placeholder="First Name" value = "<?php echo $firstName." ".$lastName; ?>"/>
									</div>
									<div class="form-group">
										<label>Student ID</label>
										<input readonly="readonly" type="text" class="form-control" name="studentID" value = "<?php echo $sID; ?>"/>
									</div>
									<div class="form-group">
										<label>Program</label>
										<input readonly="readonly" type="text" class="form-control" name="program" value = "<?php echo $program; ?>"/>
									</div>
									<div class="form-group">
										<label>Email Address</label>
										<input readonly="readonly" type="text" class="form-control" name="email" value = "<?php echo $email; ?>"/>
									</div>

									<button type="submit" class="btn btn-default btn-success btn-block">Submit</button>

								</form>
							</div>
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
								<div class = "timer2" style="color:red;text-align: center;">Reservation closes in <span id="timer2"></span> seconds!</div>
							</div>
							<div class="modal-body">
								<form id="formEdit" action="Reserve.php" method="post">
										<input readonly="readonly" type="hidden" class="form-control" name = "reservationID" id="reservationID" value="<?php echo $modReserve['reservationID']; ?>"/>
										<input readonly="readonly" type="hidden" class="form-control" name = "roomID" id="reservationID" value="<?php echo $modReserve['roomID']; ?>"/>
									<div class="form-group">
										<label>Room Name</label>
										<input readonly="readonly" type="text" class="form-control" name = "roomID" id="reservationID" value="<?php echo $roomChosen; ?>"/>
									</div>
									<div class="form-group">
										<label>Title of Reservation</label>
										<input type="text" class="form-control" placeholder="Enter a Title"  name="title" value = "<?php echo $modReserve['title']; ?>">
									</div>
									<div class="form-group">
										<label>Description of Reservation</label>
										<textarea rows="3" cols="50" placeholder="Describe the Reservation here..." class="form-control" name="description"><?php echo $modReserve['description']; ?></textarea>
									</div>
									<!-- Time slots should be inserted here-->
									<div class="form-group">
										<label>Date:</label>
										<input readonly="readonly" type="text" class="form-control" name = "dateDrop" id="dateDrop" value="<?php echo $modDate[0]; ?>"/>
										<br>										
										<label>Start Time:</label> 
											<select name = "startTime">
												<?php
												$getStartHoursSelect = true;
												getHours();
												$getStartHoursSelect = false;
												?>
											</select>&nbsp &nbsp &nbsp
										<label>End Time:</label>
											<select name = "endTime">
												<?php
												$getEndHoursSelect = true;
												getHours(TRUE);
												$getEndHoursSelect = false;
												?>
											</select>&nbsp &nbsp &nbsp
										
										<input readonly = "readonly" id = "roomOptionsMod" class="roomNum" name = "roomName" value="<?php if($roomReserve != NULL) echo $roomReserve->getName(); ?>"/>
										<input hidden name = "roomID" value="<?php if($roomReserve != NULL) echo $roomReserve->getRID(); ?>">
									</div>
									<button type="submit" name="action" value="modifying" class="btn btn-default btn-success btn-block">Submit</button>
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
								<h4>Edit any of your profile info</h4>
							</div>
							<div class="modal-body">
								<form id="profileForm" name = "form" action="ChangeDetails.php" method="post" onclick ="showResult();">
									<div class="form-group">
										<label>Name</label>
										<input readonly="readonly" type="text" class="form-control" name="firstName" placeholder="First Name" value = "<?php echo $firstName." ".$lastName; ?>"/>
									</div>
									<div class="form-group">
										<label>Student ID</label>
										<input readonly="readonly" type="text" class="form-control" name="studentID" placeholder="Student ID" value = "<?php echo $sID; ?>"/>
									</div>
									<div class="form-group">
										<label>Program</label>
										<input readonly="readonly" type="text" class="form-control" name="program" placeholder="Program" value = "<?php echo $program; ?>"/>
									</div>
									<div class="form-group">
										<label>Old Password</label>
										<input type="password" class="form-control" name="oldPass" placeholder="Old Password"/>
									</div>
									<div class="form-group">
										<label>New Password</label>
										<input type="password" class="form-control" name="newPass" placeholder="New Password"/>
									</div>
									<div class="form-group">
										<label>Current Email Address</label>
										<input readonly="readonly" type="text" class="form-control" name="oldEmail" placeholder="Email Address" value = "<?php echo $email; ?>"/>
									</div>
									<div class="form-group">
										<label>New Email Address</label>
										<input type="text" class="form-control" name="newEmail" placeholder="Email Address"/>
									</div>

									<button type="submit" class="btn btn-default btn-success btn-block">Submit</button>
								</form>
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
									
									$db->closeServerConn($conn);
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
</body>

</html>
