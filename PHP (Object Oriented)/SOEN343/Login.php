<?php
class Login{

	//to connect to Wolfcall server
	private function openConnection()
	{
	$servernamelocal = "192.168.2.36";
	$servernameremote = "wolfcall.ddns.net";
	$port = 3306;
	$username = "SOEN341user";
	$password = "G3tR3ck3dS0n";
	$schema = "soen341";
	
	$conn = new mysqli($servernameremote, $username, $password, $schema, $port);
	
	if($conn->connect_error){
		$conn  = new mysqli($servernamelocal, $username, $password, $schema, $port);
		
		if($conn->connect_error)
			die("Connection failed: " . $conn->connect_error);
	}
	return $conn;
	}
	//to close connection to Wolfcall server
	private function closeConnection()
	{
		$conn->close();
	}
	//get email from html
	function getEmailFromBootstrap()
	{
		return $_POST["email"];
	}
	//get password from html
	function getPasswordFromBootstrap()
	{
		return $_POST["password"];
	}
	//make sure the user logs in with good credentials
	private function checkUserAndPass()
	{
		openConnection();
		
		$sql = "SELECT email, password FROM student WHERE email =".getEmailFromBootstrap()." AND password = ".getPasswordFromBootstrap();
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0) {
			return true;
		}
		else 
			return false;
		
		closeConnection();
	}
	//make sure the user exists in the db (to use if wrong password)
	private function checkUserExist()
	{
		openConnection();
		
		$sql = "SELECT email, password FROM student WHERE email =".getEmailFromBootstrap();
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0) {
			return true;
		}
		else
			return false;
		
			closeConnection();
	}
	//Call this to save credentials for a new user
	function setCredentials()
	{
		openConnection();
		$sql = "INSERT INTO student (email, password)
		VALUES (".getEmailFromBootstrap().", ".getPasswordFromBootstrap().")";
		
		if ($conn->query($sql) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		$email = getEmailFromBootstrap();
		closeConnection();
	}
	//CALL THIS when a user wants to login
	function allowLogin()
	{
		if (checkUserAndPass() == true)
		{
			echo "Success";
			//line to redirect to next page
		}
		else
		{
			if (checkUserExist() == true)
			{
			alert("Wrong Password");
			}
			else 
				{
					setCredentials();					
				}	
			
		}
	}
	
}
?>