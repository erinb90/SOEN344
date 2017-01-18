<?php

class RoomMapper
{
	private $roomActive;
	private $roomData;
		
	/* 
		Constructors for the Student Mapper object
	*/
        
	public function __construct($rID, $conn) {

		if(empty($rID))
		{
			$this->roomActive = new RoomDomain();
			$this->roomData = new RoomTDG();
		}
		else
		{
			$this->roomActive = new RoomDomain();
			$this->roomData = new RoomTDG();
			
			$this->roomActive->setName($this->roomData->getName($rID, $conn));
			$this->roomActive->setLocation($this->roomData->getLocation($rID, $conn));
			$this->roomActive->setDescription($this->roomData->getDescription($rID, $conn));
			$this->roomActive->setBusy($this->roomData->checkBusy($rID, $conn));
			$this->roomActive->setRID($rID);
		}
	}
	
	/* Get methods for the Student Domain object
	*/
	public function getName(){
		return $this->roomActive->getName();
    }
    
    public function getLocation() {
        return $this->roomActive->getLocation();
    }
    
    public function getDescription() {
        return $this->roomActive->getDescription();
    }
	 
	public function getRID(){
		return $this->roomActive->getRID();
    }

	public function getBusy(){
        return $this->roomActive->getBusy();
    }
	
	public function setBusy($status){
        return $this->roomActive->setBusy($status);
    }

	/*
		TDG Functions
	*/
	public function checkBusy($rID, $conn){
        return $this->roomData->checkBusy($rID, $conn);
    }
	
	/*
		Unit of Work (TDG Functions for Room)
	*/
	public function updateRoom($roomUpdateList, $conn){
        $this->roomData->updateRoom($roomUpdateList, $conn);
    }
	
}
?>
