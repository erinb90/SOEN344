<?php


class RoomTDG extends TDG
{

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

    public function findAll()
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable());
        $sth = $query->execute();
        $m = $sth->fetchAll();
        return $m;
    }

	public function getAllRooms($conn)
    {
		
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

    public function getPk()
    {
        return "roomID";
    }

    public function getTable()
    {
        return "room";
    }

    public function insert(DomainObject &$object)
    {
        // TODO: Implement insert() method.
    }

    public function delete(DomainObject &$object)
    {
        // TODO: Implement delete() method.
    }

    public function update(DomainObject &$object)
    {
        // TODO: Implement update() method.
    }

    public function findByPk($id)
    {
        $query = Registry::getConnection()->createQueryBuilder();
        $query->select("*");
        $query->from($this->getTable(), $this->getTable());
        $query->where($this->getTable() . '.' . $this->getPk() . "='" . $id . "'");
        $sth = $query->execute();
        $m = $sth->fetchAll();

        return $m[0];
    }
}
