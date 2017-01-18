<?php

// Start the session
session_start();

include_once dirname(__FILE__).'/../Utilities/ServerConnection.php';

include "StudentDomain.php";
include "StudentTDG.php";

class StudentMapper
{
	private $studentActive;
	private $studentData;
		
	/* 
	*	Constructors for the Student Mapper object
	*	If the parameter is null, the student is logging in from the Index page
	*	In which case they simply need access to the checkUserAndPass or checkUserExist methods
	*	No variables need to be instantiated until they actually log in
	*/
	public function __construct($email, $conn) {

		if(empty($email))
		{
			$this->studentActive = new StudentDomain();
			$this->studentData = new StudentTDG();
		}
		else
		{
			$this->studentActive = new StudentDomain();
			$this->studentData = new StudentTDG();
		
			$this->setFirstName($this->studentData->getFirstName($email, $conn));
			$this->setLastName($this->studentData->getLastName($email, $conn));
			$this->setEmailAddress($email);
			$this->setProgram($this->studentData->getProgram($email, $conn));
			$this->setSID($this->studentData->getSID($email, $conn));			
		}
	}

	public function checkUserAndPass($email, $pass, $conn){
		return $this->studentData->checkUserAndPass($email, $pass, $conn); 
	}
	
	public function checkUserExist($email, $conn){
		return $this->studentData->checkUserExist($email, $conn);
	}
	
	/* Set methods for the Student Domain object
	*/
	public function setFirstName($first){
		$this->studentActive->setFirstName($first);
    }
    
    public function setLastName($last){
		$this->studentActive->setLastName($last);
    }
    
    public function setEmailAddress($new) {
        $this->studentActive->setEmailAddress($new);
    }
    
    public function setProgram($program) {
        $this->studentActive->setProgram($program);
    }
	
	public function setSID($sID) {
        $this->studentActive->setSID($sID);
    }
	
	public function setNewPassword($oldPass,$newPass, $conn){
		
		$hashOld = $this->studentData->hashPassword($oldPass, $conn);
		$hashNew = $this->studentData->hashPassword($newPass, $conn);
		
		$this->studentActive->setOldPassword($hashOld);
		$this->studentActive->setNewPassword($hashNew);
	}
	
	public function setNewEmail($newEmail) {
        $this->studentActive->setNewEmail($newEmail);
    }
	
	/* Get methods for the Student Domain object
	*/
	public function getFirstName(){
		return $this->studentActive->getFirstName();
    }
    
    public function getLastName(){
		return $this->studentActive->getLastName();
    }
    
    public function getEmailAddress() {
        return $this->studentActive->getEmailAddress();
    }
    public function getEmailAddressFromDB($email, $conn) {
    	return $this->studentData->getEmailAddress($email, $conn);
    }
    
    public function getProgram() {
        return $this->studentActive->getProgram();
    }
	
	public function getSID() {
        return $this->studentActive->getSID();
    }
	
	public function getNewPass() {
        return $this->studentActive->getNewPass();
    }
	
	public function getOldPass() {
        return $this->studentActive->getOldPass();
    }
	
	public function getNewEmail() {
        return $this->studentActive->getNewEmail();
    }
		
	/* 
		The Update methods for all Entities in the Student table can be found here
		Student ID cannot be updated
		First Name cannot be updated
		Last Name cannot be updated
		Program cannot be updated
     */
        
	/*
		Unit of Work (TDG Functions for Student)
	*/
	public function updateStudent($studentUpdateList, $conn){
        $this->studentData->updateStudent($studentUpdateList, $conn);
    }
}
?>
