<?php



class StudentMapper extends AbstractMapper
{

	/**
	 * @var \TDG
	 */
	private $_StudentTDG;
		
	/* 
	*	Constructors for the Student Mapper object
	*	If the parameter is null, the student is logging in from the Index page
	*	In which case they simply need access to the checkUserAndPass or checkUserExist methods
	*	No variables need to be instantiated until they actually log in
	*/
	public function __construct()
	{
		$this->_StudentTDG = new StudentTDG();
	}

	public function checkUserAndPass($email, $pass, $conn)
	{
		return $this->_StudentTDG->checkUserAndPass($email, $pass, $conn);
	}
	
	public function checkUserExist($email, $conn){
		return $this->_StudentTDG->checkUserExist($email, $conn);
	}
	
	/* Set methods for the Student Domain object
	*/
	public function setFirstName($first)
	{
		$this->studentActive->setFirstName($first);
    }
    
    public function setLastName($last){
		$this->studentActive->setLastName($last);
    }
    
    public function setEmailAddress($new)
	{
        $this->studentActive->setEmailAddress($new);
    }
    
    public function setProgram($program)
	{
        $this->studentActive->setProgram($program);
    }
	
	public function setSID($sID)
	{
        $this->studentActive->setSID($sID);
    }
	
	public function setNewPassword($oldPass,$newPass, $conn)
	{
		
		$hashOld = $this->_StudentTDG->hashPassword($oldPass, $conn);
		$hashNew = $this->_StudentTDG->hashPassword($newPass, $conn);
		
		$this->studentActive->setOldPassword($hashOld);
		$this->studentActive->setNewPassword($hashNew);
	}
	
	public function setNewEmail($newEmail)
	{
        $this->studentActive->setNewEmail($newEmail);
    }
	
	/* Get methods for the Student Domain object
	*/
	public function getFirstName()
	{
		return $this->studentActive->getFirstName();
    }
    
    public function getLastName()
	{
		return $this->studentActive->getLastName();
    }
    
    public function getEmailAddress()
	{
        return $this->studentActive->getEmailAddress();
    }
    public function getEmailAddressFromDB($email, $conn)
	{
    	return $this->_StudentTDG->getEmailAddress($email, $conn);
    }

    public function findByEmail($username)
	{
		return $this->getModel($this->_StudentTDG->findByEmail($username));
	}
    
    public function getProgram()
	{
        return $this->studentActive->getProgram();
    }


	public function getSID()
	{
        return $this->studentActive->getSID();
    }
	
	public function getNewPass()
	{
        return $this->studentActive->getNewPass();
    }
	
	public function getOldPass()
	{
        return $this->studentActive->getOldPass();
    }

    /**
     * @return array an array of student objects
     */
    public function getAllStudents()
    {
        $data = $this->_StudentTDG->findAll();
        $models = array();
        foreach ($data as $row)
        {
            $models[] = $this->getModel($row);
        }
        return $models;
    }

	public function getNewEmail()
	{
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
	public function updateStudent($studentUpdateList, $conn)
	{
        $this->_StudentTDG->updateStudent($studentUpdateList, $conn);
    }

	/**
	 * @param \stdClass $data
	 *
	 * @return StudentDomain
	 */
	public function getModel($data)
	{
		if(!$data)
			return  null;

		$StudentDomain = new StudentDomain();
		$StudentDomain->setEmailAddress($data['email']);
		$StudentDomain->setPassword($data['password']);
		$StudentDomain->setFirstName($data['firstName']);
		$StudentDomain->setLastName($data['lastName']);
		$StudentDomain->setSID($data['studentID']);
		$StudentDomain->setProgram($data['program']);


		return $StudentDomain;
	}

	/**
	 * @param \DomainObject $object
	 *
	 * @return mixed
	 */
	public function insert(DomainObject &$object)
	{
		return $this->_StudentTDG->insert($object);

	}

	/**
	 * @param \DomainObject $object
	 *
	 * @return mixed
	 */
	public function delete(DomainObject &$object)
	{
		return $this->_StudentTDG->delete($object);
	}

	/**
	 * @param \DomainObject $object
	 *
	 * @return mixed
	 */
	public function update(DomainObject &$object)
	{
		return $this->_StudentTDG->update($object);
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function findByPk($id)
	{
		return $this->getModel($this->_StudentTDG->findByPk($id));
	}
}
?>
