<?php
namespace CL_Test\V1;
class RelationAdd 
{
	private $conn;	
	public function __construct($conn)
	{
        $this->conn = $conn;	
	}	
	
	public function insertRelation($personId, $addressId)
	{
		try 
   	{		
		// prepare Person-Address Relation sql and bind parameters
    	$stmt = $this->conn->prepare("INSERT INTO person_address_rel (PersonID, AddressID, Active)
    	VALUES (:personID, :addressID, :active)");
    	$stmt->bindParam(':personID', $personID);
    	$stmt->bindParam(':addressID', $addressID);
    	$stmt->bindParam(':active', $relationActive);
    	
    	// insert Person-Address Relation row
    	$personID = $personId;
    	$addressID = $addressId;
    	$relationActive = true;
    	
    	$stmt->execute();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}    	
    	// fetch new ID
    	$lastRelationId = $this->conn->lastInsertId();
    	return $lastRelationId;
	}
}
