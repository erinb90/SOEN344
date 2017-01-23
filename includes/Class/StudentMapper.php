<?php

class StudentMapper extends AbstractMapper
{


	protected $tdg;
	/**
	 * StudentMapper constructor.
	 */
	public function __construct()
	{
		$this->tdg = new StudentTDG();
	}

    /**
     * @return array an array of student objects
     */
    public function getAllStudents()
    {
        $data = $this->getTdg()->findAll();
        $models = array();
        foreach ($data as $row)
        {
            $models[] = $this->getModel($row);
        }
        return $models;
    }

	/**
	 * @param \DomainObject|StudentDomain $data
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
	 * @return StudentTDG
	 */
	public function getTdg()
	{
		return $this->tdg;
	}
}
?>
