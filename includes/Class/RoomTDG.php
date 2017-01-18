<?php


class RoomTDG
{
    /*
		No Default Constructor is Necessary for this class
	*/
	/* 
		No Set methods are necessary for this class, as the Student cannot edit the information for any Room
	*/
	
	/* 
		No insert/update methods are necessary for this class, as the Student cannot edit the information for any Room
	*/
	
	/* 
		The Get methods for all Entities in the room table can be found here
     */
    public function getName($rID, $conn){
	
		$sql = "SELECT name FROM room WHERE roomID ='".$rID."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["name"];
    }
    
    public function getRoomID($rID, $conn){
		
		$sql = "SELECT roomID FROM room WHERE roomID ='".$rID."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["roomID"];
    }
  
    public function getLocation($rID, $conn){
		
		$sql = "SELECT location FROM room WHERE roomID ='".$rID."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["location"];
    }
	
	public function getDescription($rID, $conn){
		
		$sql = "SELECT description FROM room WHERE roomID ='".$rID."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		return $row["description"];
    }
	
	public function getAllRooms($conn){
		
		$sql = "Select * from room";
		$result = $conn->query($sql);
		
		$resultSet = array();
		
		while($row = $result->fetch_assoc()){
			$resultSet[] = $row;
		}
		
		return $resultSet;
	}
	
	public function checkBusy($rID, $conn){
		
		$sql = "Select busy from room WHERE roomID ='".$rID."'";
		$result = $conn->query($sql);
		
		$row = $result->fetch_assoc();
		
		return $row["busy"];
	}

	public function updateRoom($roomUpdateList, $conn){
				
		foreach($roomUpdateList as &$roomUpdated)
		{
			$sql = "Update room SET busy ='".$roomUpdated->getBusy()."' WHERE roomID ='".$roomUpdated->getRID()."'";
			$result = $conn->query($sql);
		}
	}
}
