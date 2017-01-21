<?php

// Start the session
session_start();

class ReservationDomain
{
	private $reID;	
	private $sID;	
	private $rID;
	private $startTimeDate;
	private $endTimeDate;
	private $title;
	private $description;
	private $wait;
	
	
	/* Default Constructor for the Room Domain object
	*/
	public function __construct()
    {

    }
	
	/* Get methods for the Reservation Domain object
	*/
	public function getID(){
		return $this->reID;
    }
    
    public function getSID() {
        return $this->sID;
    }
    
    public function getRID() {
        return $this->rID;
    }
	
	public function getStartTimeDate() {
        return $this->startTimeDate;
    }	
	
	public function getEndTimeDate() {
        return $this->endTimeDate;
    }
	
	public function getTitle() {
        return $this->title;
    }
	
	public function getDescription() {
        return $this->description;
    }
	
	public function getWait() {
        return $this->wait;
    }
	
	/* Set methods for the Reservation Domain object
	*/
	public function setREID($reID){
		$this->reID = $reID;
    }
    
    public function setSID($sID) {
        $this->sID = $sID;
    }
    
    public function setRID($rID) {
        $this->rID = $rID;
    }
	
	public function setStartTimeDate($start) {
        $this->startTimeDate = $start;
    }	
	
	public function setEndTimeDate($end) {
        $this->endTimeDate = $end;
    }
	
	public function setTitle($title) {
        $this->title = $title;
    }
	
	public function setDescription($desc) {
		$this->description = $desc;
    }
	
	public function setWait($wait) {
		$this->wait = $wait;
    }
}
?>