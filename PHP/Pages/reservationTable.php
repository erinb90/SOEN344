<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "../Class/ReservationMapper.php";
include dirname(__FILE__)."/../Utilities/tableHelper.php";
include "../Class/RoomList.php";
include_once dirname(__FILE__).'/../Utilities/ServerConnection.php';

$db = new ServerConnection();
$conn = $db->getServerConn();

$rooms = new RoomList($conn);
$reserve = new ReservationMapper();
echo "<br />"; 

if(isset($_GET["Date"])){
	$date = explode("/", $_GET["Date"]);
	//var_dump($date);
	$today = $reserve->getReservationsByDate($date[2]."/".$date[1]."/".$date[0], $conn);
}else{
	$today = date("d/m/Y");
	$today = $reserve->getReservationsByDate($today, $conn);
}

$thelper = new tableHelper();
						
$params = array("class"=>"reservations", "id"=>"reservations");

$table = $thelper->initTable($params);
$values = array();
$classes = array();

for($x = 0 ; $x < 24 ; $x++){
	$values[] = sprintf("%02.0f",$x).":00";
}
$table .= $thelper->populateHeader(array("class"=>"time", "id"=>"time","colspan" => "2"), "time", $values, " ", "date", "datetoday");

$roomRes = $rooms->getRoomList();

foreach($today as $res){
	$start = $res->getStartTimeDate();
	$start = explode(" ", $start);
	$start = explode(":", $start[1]);
	$end = $res->getEndTimeDate();
	$end = explode(" ", $end);
	
	if(strcmp($end[1],"00:00")==0)
		$end[1] = "24:00";
			
	$end = explode(":", $end[1]);
	if($start[1] > $end[1]){
		$add_minus = -1;
	}else{
		$add_minus = 1;
	}
	$slots = (2*($end[0] - $start[0])) + (($end[1] == $start[1])?0:$add_minus);
	$begin = (2*$start[0]) + ((strcmp($start[1],"00")==0)?0:1);
	
	for($x = 0 ; $x < $slots ; $x++){
		$id = $res->getRID();
		$pos = $begin + $x;
		//$roomRes[$id][1][$pos] = "11";
		if($res->getSID() == $_SESSION["sIDForTable"])
			$roomRes[$id][2][$pos] = 'yourBooking';
		else
			$roomRes[$id][2][$pos] = 'booked';
	}
}

foreach($roomRes as $val){
	$table .= $thelper->populateRow(array("class"=>"slot","id"=>"slot","colspan" => "1"), $val[2], $val[1], $val[0]->getName()." (".$val[0]->getLocation().")", "room");
}

$table .= $thelper->closeTable();

echo $table;

$db->closeServerConn($conn);
?>