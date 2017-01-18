<?php

class ReservationMapper
{
	private $reservationActive;
	private $reservationData;
		
	public function __construct($reID, $conn) {

		if(empty($reID))
		{
			$this->reservationActive = new ReservationDomain();
			$this->reservationData = new ReservationTDG();
		}
		else
		{
			$this->reservationActive = new ReservationDomain();
			$this->reservationData = new ReservationTDG();
			
			$this->reservationActive->setSID($this->reservationData->getStudentID($reID, $conn));
			$this->reservationActive->setRID($this->reservationData->getRoomID($reID, $conn));
			$this->reservationActive->setStartTimeDate($this->reservationData->getStart($reID, $conn));
			$this->reservationActive->setEndTimeDate($this->reservationData->getEnd($reID, $conn));
			$this->reservationActive->setTitle($this->reservationData->getTitle($reID, $conn));
			$this->reservationActive->setDescription($this->reservationData->getDescription($reID, $conn));
			$this->reservationActive->setREID($reID);
		}
	}
	/*
	public function addReservation($sID, $rID, $start, $end, $title, $desc, $conn, $wait){
		$this->reservationData->addReservation($sID, $rID, $start, $end, $title, $desc, $conn, $wait);
    }	
	*/
	/* Set methods for the Reservation Domain object
	*/
	public function setREID($reID){
		$this->reservationActive->setREID($reID);
    }
    
    public function setSID($sID) {
        $this->reservationActive->setSID($sID);
    }
    
    public function setRID($rID) {
        $this->reservationActive->setRID($rID);
    }
	
	public function setStartTimeDate($start) {
        $this->reservationActive->setStartTimeDate($start);
    }	
	
	public function setEndTimeDate($end) {
        $this->reservationActive->setEndTimeDate($end);
    }
	
	public function setTitle($title) {
        $this->reservationActive->setTitle($title);
    }
	
	public function setDescription($desc) {
		$this->reservationActive->setDescription($desc);
    }
	
	public function setWait($wait) {
		$this->reservationActive->setWait($wait);
    }
	
	/* Get methods for the Reservation Domain object
	*/
	
	/*	The REID takes the sID as a parameter so it can find the appropriate information of the student's reservation
	*	Only get from the database
	*/
	public function getReservation($reID, $conn){
		return $this->reservationData->getReservation($reID, $conn);
	}
	
	public function getREID($sID, $conn){
		return $this->reservationData->getREID($sID, $conn);
    }
    
	public function getID($sID, $conn){
		return $this->reservationActive->getID();
    }
	
    public function getSID(){
        return $this->reservationActive->getSID();
    }
    
    public function getRID(){
        return $this->reservationActive->getRID();
    }
	
	public function getStartTimeDate(){
        return $this->reservationActive->getStartTimeDate();
    }	
	
	public function getEndTimeDate(){
        return $this->reservationActive->getEndTimeDate();
    }
	
	public function getTitle(){
       return $this->reservationActive->getTitle();
    }
	
	public function getDescription(){
        return $this->reservationActive->getDescription();
    }
	
	public function getWait(){
        return $this->reservationActive->getWait();
    }
    
	public function getReservations($sID, $conn){
		return $this->reservationData->getReservations($sID, $conn);
	}

	public function getReservationsByDate($start, $conn) {
		return $this->reservationData->getReservationsByDate($start, $conn);
	}

	public function getReservationsByRoomAndDate($rID, $start, $wait, $conn) {
		return $this->reservationData->getReservationsByRoomAndDate($rID, $start, $wait, $conn);
	}

	public function getWaitlistIDByStudent($sID, $reID, $conn) {
		return $this->reservationData->getWaitlistIDByStudent($sID, $reID, $conn);
	}

	public function getReservationsBySIDAndDate($sID, $start, $conn) {
		return $this->reservationData->getReservationsBySIDAndDate($sID, $start, $conn);
	}

	/*
		Unit of Work (TDG Functions for Room)
	*/	
	public function deleteReservation($reservationDeletedList, $conn) {
			$this->reservationData->deleteReservation($reservationDeletedList, $conn);
	}
	
	public function addReservation($reservationNewList, $conn) {
		$this->reservationData->addReservation($reservationNewList, $conn);
	}
	
	public function updateReservation($reservationUpdateList, $conn) {
		$this->reservationData->updateReservation($reservationUpdateList, $conn);
	}
}
?>
