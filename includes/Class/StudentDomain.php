<?php



class StudentDomain
{
	private $firstName = "";
    private $lastName = "";
    private $emailAddress = "";
	private $newEmail = "";
    private $program = "";
	private $sID = "";
	private $oldPass = "";
	private $newPass = "";

    private $_password;
	/*
		No Default Constructor is Necessary for this class
	*/
	
	/* Get methods for the Student Domain object
	*/
	public function getFirstName(){
		return $this->firstName;
    }
    
    public function getLastName(){
		return $this->lastName;
    }
    
    public function getEmailAddress() {
        return $this->emailAddress;
    }
    
    public function getProgram() {
        return $this->program;
    }
	
	public function getSID() {
        return $this->sID;
    }
	
	public function getOldPass() {
        return $this->oldPass;
    }
	
	public function getNewPass() {
        return $this->newPass;
    }
	
	public function getNewEmail() {
        return $this->newEmail;
    }
	
	/* 
		Set methods for the Student Domain object
	*/
	public function setFirstName($first){
		$this->firstName = $first;
    }
    
    public function setLastName($last){
		$this->lastName = $last;
    }
    
    public function setEmailAddress($email) {
        $this->emailAddress = $email;
    }
	
	 public function setNewEmail($newEmail) {
        $this->newEmail = $newEmail;
    }

    public function getPassword()
    {
        return $this->_password;
    }

    public function setPassword($pass)
    {
        $this->_password = $pass;
    }
    
    public function setProgram($program) {
        $this->program = $program;
    }
	
	public function setSID($sID) {
        $this->sID = $sID;
    }
	
	public function setOldPassword($oldPass) {
        $this->oldPass = $oldPass;
    }
	
	public function setNewPassword($newPass) {
        $this->newPass = $newPass;
    }

    public function getFullname()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }
}
?>