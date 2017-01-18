<?php

class UnitOfWork
{
	private $conn;
	/*
		Room can be modified (to set the room as locked or not)
		Room cannot be deleted
		Room cannot be inserted
	*/
	private $roomUpdateList;
	/*
		Student can be modified (only for email and password)
		Student cannot be deleted
		Student cannot be inserted
	*/
	private $studentUpdateList;
	/*
		Reservations can be modified
		Reservations can be created
		Reservations can be deleted
	*/
	private $reservationNewList;
	private $reservationUpdateList;
	private $reservationDeletedList;
	
	public function __construct($conn) {

		$temp = "";
		$this->conn = $conn;
		
		$this->roomUpdateList = array();
		$this->studentUpdateList = array();
		$this->reservationNewList = array();
		$this->reservationUpdateList = array();
		$this->reservationDeletedList = array();
	}
	
	/* 
		Trigger Modification of a Room
	*/
	public function registerDirtyRoom($roomObject){
		
		array_push($this->roomUpdateList, $roomObject);
	}
	
	/* 
		Trigger Modification of a Student
	*/
	public function registerDirtyStudent($studentObject){
		
		array_push($this->studentUpdateList, $studentObject);
	}

	/*
		Trigger Modification of a Reservation
	*/
	public function registerDirtyReservation($reservationObject){
		array_push($this->reservationUpdateList, $reservationObject);
	}
	
	public function registerNewReservation($reservationObject){
		
		array_push($this->reservationNewList, $reservationObject);
	}
	
	public function registerDeletedReservation($reservationObject){
		
		array_push($this->reservationDeletedList, $reservationObject);
	}
	
	/*
		Finalize All Data to be updated
	*/
	public function commit()
	{
		$room = new RoomMapper();
		$student = new StudentMapper();
		$reservation = new ReservationMapper();
		
		//Commit changes to the Room
		$room->updateRoom($this->roomUpdateList, $this->conn);
		
		//Commit changes to the Student
		$student->updateStudent($this->studentUpdateList, $this->conn);
		
		//Commit change to the Reservations	
		$reservation->addReservation($this->reservationNewList, $this->conn);
		$reservation->deleteReservation($this->reservationDeletedList, $this->conn);
		$reservation->updateReservation($this->reservationUpdateList, $this->conn);
		
		//Clear the lists after completetion
		$this->reservationNewList = array();
		$this->reservationDeletedList = array();
		$this->reservationUpdateList = array();
		$this->studentUpdateList = array();
		$this->roomUpdateList = array();
	}
}
?>