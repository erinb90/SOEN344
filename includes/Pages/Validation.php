<?php
include "../Class/StudentMapper.php";
include_once dirname(__FILE__).'/../Utilities/ServerConnection.php';

$db = new ServerConnection();
$conn = $db->getServerConn();

$email = htmlspecialchars($_POST["email"]);
$password = htmlspecialchars($_POST["password"]);

$_SESSION["email"] = $email;

$wrongPass = "Invalid Password Entered. Please try again";
$noUser = "Account not Found. Please enter an email for an Existing Account";
$blankEmail = "The email field cannot be blank. Please enter a valid account email address";
$blankPass = "The password field cannot be blank. Please enter a valid password for your account";
    
if (empty($email))
{
    echo "<script type='text/javascript'>alert('$blankEmail');
		window.location.replace('../../index.php');
		</script>";
}
elseif (empty($password))
{
    echo "<script type='text/javascript'>alert('$blankPass');
		window.location.replace('../../index.php');
		</script>";
}
    
$log = new StudentMapper($email, $conn);
$validate = $log->checkUserAndPass($email,$password, $conn);
$exist = $log->checkUserExist($email, $conn);

$_SESSION["sIDForTable"] = $validate;

$db->closeServerConn($conn);

if ($validate == true)
{
	header("Location: ../Pages/Home.php");
}
else
{
	if ($exist == true)
	{
		echo "<script type='text/javascript'>alert('$wrongPass');
		window.location.replace('../../index.php');
		</script>";
	}
	else 
	{
		echo "<script type='text/javascript'>alert('$noUser');
		window.location.replace('../../index.php');
		</script>";
	}	
}
?>