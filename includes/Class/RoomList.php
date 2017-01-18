<?php


class RoomList {
	//put your code here
	
	private $roomList;
	private $tdg;
	
	public function __construct($conn) {
		$this->roomList = array();
		$this->tdg = new RoomTDG();
		//$this->roomList = $this->tdg->getAllRooms();
		
		
		foreach($this->tdg->getAllRooms($conn) as $val){
			$tmp = new RoomDomain();
			$tmp->setRID($val["roomID"]);
			$tmp->setName($val["name"]);
			$tmp->setLocation($val["location"]);
			$tmp->setDescription($val["description"]);
			$this->roomList[$val["roomID"]][0] = $tmp;
			$values = array();
			$classes = array();
			for($x = 0 ; $x < 48 ; $x++){
				$values[] = "";
				$classes[] = "";
			}
			$this->roomList[$val["roomID"]][1] = $values;
			$this->roomList[$val["roomID"]][1] = $classes;
		}
		
	}
	
	public function getRoomList(){
		return $this->roomList;
	}
}
