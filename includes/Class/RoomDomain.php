<?php

class RoomDomain implements DomainObject
{
	private $name = "";
    private $location = "";
    private $description = "";
	private $rID = "";
	private $busy = "";
	
	/*
		No Default Constructor is Necessary for this class
	*/
	
	/* Get methods for the Room Domain object
	*/
	public function getName(){
		return $this->name;
    }
    
    public function getLocation() {
        return $this->location;
    }
    
    public function getDescription() {
        return $this->description;
    }
	
	public function getRID() {
        return $this->rID;
    }
	
	public function getBusy() {
        return $this->busy;
    }
	
	/* Set methods for the Room Domain object
	*/
	public function setName($name){
		$this->name = $name;
    }
    
    public function setLocation($location){
		$this->location = $location;
    }
    
    public function setDescription($desc) {
        $this->description = $desc;
    }
      
	public function setRID($rID) {
        $this->rID = $rID;
    }
	
	public function setBusy($busy) {
        $this->busy = $busy;
    }
}