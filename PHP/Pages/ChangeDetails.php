<?php
include "../Class/StudentMapper.php";
include "../Class/RoomMapper.php";
include "../Class/ReservationMapper.php";
include "../Class/Unit.php";
include_once dirname(__FILE__).'/../Utilities/ServerConnection.php';

// Start the session
session_start();

$db = new ServerConnection();
$conn = $db->getServerConn();

$unit = new UnitOfWork($conn);

$oldPass = htmlspecialchars($_POST["oldPass"]);
$newPass = htmlspecialchars($_POST["newPass"]);

$oldEmail = htmlspecialchars($_POST["oldEmail"]);
$newEmail = htmlspecialchars($_POST["newEmail"]);

$student = new StudentMapper($oldEmail, $conn);

$begin = "You have successfully changed your";
$changePass = " password";
$changeEmail = " email";
$msg = "";

//Both Email and password are empty
if(empty($newEmail) and empty($newPass) and empty($oldPass))
{
	//Tell user that he tried to submit an empty form and do nothing
	$msg = "No Data has been saved!";
	$_SESSION["msgClass"] = "failure";
}
//If Email only is empty
else if (empty($newEmail))
{
	//Set the new password in the active
	$student->setNewPassword($oldPass, $newPass, $conn);
	$unit->registerDirtyStudent($student);	
}
//If New Password only is empty
else if (empty($newPass))
{
	//Check to see if new email already exists in the DB	
	$checkEmail = $student->getEmailAddressFromDB($newEmail, $conn);
	if (empty($checkEmail))
	{
		$student->setNewEmail($newEmail);
		$unit->registerDirtyStudent($student);
		
		$_SESSION["email"] = $newEmail;
	}
	else 
	{
		$_SESSION["userMSG"] = "Email already exists! Please select a new one!";
		$_SESSION["msgClass"] = "failure";
	}
}
//If both fields are filled
else
{
	//Check to see if new email already exists in the DB
	$checkEmail = $student->getEmailAddressFromDB($newEmail, $conn);
	if (empty($checkEmail))
	{
		//Need to implement the checker to know what it changed
		$student->setNewPassword($oldPass, $newPass, $conn);
		$student->setNewEmail($newEmail);
		
		if($temp === true)
		{
			$msg = $begin.$changeEmail." and".$changePass."!";
		}
		else
		{
			$msg = $begin.$changeEmail.", but your current password is not the one you entered. Please try again!";
		}

		$unit->registerDirtyStudent($student);
		
		$_SESSION["email"] = $newEmail;
		$_SESSION["msgClass"] = "success";
	}
	else 
	{
		$msg = "Email already exists! Please select a new one.";
		$_SESSION["msgClass"] = "failure";
	}
}

$_SESSION["userMSG"] = $msg;

$unit->commit();
$db->closeServerConn($conn);

header("Location: Home.php");
?>