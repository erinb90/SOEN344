<?php



class StudentTDG extends TDG
{
	/*
		No Default Constructor is Necessary for this class
	*/
		
	/* 
		No insert methods are necessary for this class, as the Student cannot edit the information for any Room
	*/
	
	//Verify User Credentials
    public function checkUserAndPass($email, $pass, $conn){
		
		$sql = "SELECT studentId, email, password FROM student WHERE email ='".$email."' AND password = password('".$pass."')";
		$result = $conn->query($sql);
		
		if ($result->num_rows == 1){
			$row = $result->fetch_assoc();
			return $row["studentId"];
		}else 
			return false;
	}


    public function findByEmail($username)
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable(), $this->getTable());
        $query->where($this->getTable() . '.' . "email" . "='" . $username . "'");
        $sth = $query->execute();
        $m = $sth->fetchAll();

        return $m[0];
    }

	//Verify User exsitence in the db
	public function checkUserExist($email, $conn){
		
		$sql = "SELECT * FROM student WHERE email ='".$email."'";
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0)
			return true;
		else 
			return false;
	}
		
	/* 
		The Get methods for all Entities in the Student table can be found here
    */
    public function getFirstName($email, $conn){
		
		$sql = "SELECT firstName FROM student WHERE email ='".$email."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["firstName"];
    }
    
    public function getLastName($email, $conn) {
		
		$sql = "SELECT lastName FROM student WHERE email ='".$email."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["lastName"];
    }
  
    public function getEmailAddress($email, $conn) {
		
		$sql = "SELECT email FROM student WHERE email ='".$email."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["email"];
    }
    
    public function getProgram($email, $conn) {
		
		$sql = "SELECT program FROM student WHERE email ='".$email."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["program"];
    }
	
	public function getSID($email, $conn) {
		
		$sql = "SELECT studentID FROM student WHERE email ='".$email."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["studentID"];
    }
	
	public function getPassword($email, $conn) {
		
		$sql = "SELECT password FROM student WHERE email ='".$email."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["password"];
    }
	
	/* 
		The Update methods for all Entities in the Student table can be found here
		Student ID cannot be updated
		First Name cannot be updated
		Last Name cannot be updated
		Program cannot be updated
     */

    public function updateEmailAddress($email, $new, $conn) {
		
		$sql = "Update student SET email ='".$new."' WHERE email ='".$email."' ";
		$result = $conn->query($sql);
    }
	
	//	Should be called by a changedetails.php file
	//	Should validate previous password to set new one
	
	public function updatePassword($email, $oldPass, $newPass, $conn) {
		
		$sql = "Update student SET password = password('".$newPass."') WHERE email ='".$email."' AND password = password('".$oldPass."')";
		$result = $conn->query($sql);
		
		if ($conn->affected_rows > 0)
		{
			$_SESSION["msgClass"] = "success";
			return true;
		}
		else
		{
			$_SESSION["msgClass"] = "failure";
			return false;
		}
    }
	
	//Encrypt password for local storing	
	public function hashPassword($pass, $conn){
		
		$sql = "SELECT password('".$pass."') as hashed";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["hashed"];
	}

	/*
		Unit of Work (TDG Functions for Student)
	*/
	public function updateStudent($studentUpdateList, $conn){
       
	    foreach($studentUpdateList as &$studentUpdated)
		{
			//When both the new password and old password fields are empty
			//We will only update the email
			if(empty($studentUpdated->getNewPass()) and empty($studentUpdated->getOldPass())){
				//Then only update the Email
				$sql = "Update student SET email ='".$studentUpdated->getNewEmail()."' WHERE email ='".$studentUpdated->getEmailAddress()."' ";
				$result = $conn->query($sql);
				
				if ($conn->affected_rows > 0){
					$_SESSION["msgClass"] = "success";
					$_SESSION["userMSG"] = "You have successfully changed your Email!";
				}
				else{
					$_SESSION["msgClass"] = "failure";
					$_SESSION["userMSG"] = "You cannot change your email at this time!";
				}
			}
			//When the email field are empty
			//We will only update the Password
			else if(empty($studentUpdated->getNewEmail())){
				if(empty($studentUpdated->getOldPass())){
					$_SESSION["msgClass"] = "failure";
					$_SESSION["userMSG"] = "You have left the old password field empty! Please try again!";
				}
				else{
					//Then only update the Password
					$sql = "Update student SET password = '".$studentUpdated->getNewPass()."' WHERE email ='".$studentUpdated->getEmailAddress()."' AND password = '".$studentUpdated->getOldPass()."'";
					$result = $conn->query($sql);

					if ($conn->affected_rows > 0){
						$_SESSION["msgClass"] = "success";
						$_SESSION["userMSG"] = "You have successfully changed your password!";
					}
					else{
						$_SESSION["msgClass"] = "failure";
						$_SESSION["userMSG"] = "Your current password is not the one you entered. Please try again!";
					}
				}
			}
			else
			{
				//Then update the Password first
				$sql = "Update student SET password = '".$studentUpdated->getNewPass()."' WHERE email ='".$studentUpdated->getEmailAddress()."' AND password = '".$studentUpdated->getOldPass()."'";
				$result = $conn->query($sql);
				$_SESSION["msgClass"] = "success";
				
				if ($conn->affected_rows > 0) {
					$temp = "You have successfully changed your password!";
				}
				else{
					$_SESSION["msgClass"] = "failure";
					$temp = $temp. "Your current password is not the one you entered!";
				}
				
				//Then only update the Email
				$sql = "Update student SET email ='".$studentUpdated->getNewEmail()."' WHERE email ='".$studentUpdated->getEmailAddress()."' ";
				$result = $conn->query($sql);
				
				if ($conn->affected_rows > 0){
					$temp = $temp." You have changed your Email!";
				}
				else{
					$_SESSION["msgClass"] = "failure";
					$temp = $temp." You have not changed your Email!";
				}
				$_SESSION["userMSG"] = $temp;
			}
		}
    }

    public function getPk()
    {
        return "studentID";
    }

    public function getTable()
    {
        return "students";
    }

    public function insert(DomainObject &$object)
    {
        // TODO: Implement insert() method.
    }

    public function delete(DomainObject &$object)
    {
        // TODO: Implement delete() method.
    }

    public function update(DomainObject &$object)
    {
        // TODO: Implement update() method.
    }

    public function findByPk($id)
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable(), $this->getTable());
        $query->where($this->getTable() . '.' . $this->getPk() . "='" . $id . "'");
        $sth = $query->execute();
        $m = $sth->fetchAll();



        return $m[0];
    }
}
?>