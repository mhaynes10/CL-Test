<?php
namespace CL_Test\V1;
class RelationUpdate 
{
	private $conn;	
	public function __construct($conn)
	{
        $this->conn = $conn;	
	}	
	
	public function updateRelation($relId, $personId, $addressId)
	{
		// prepare Person-Address Relation sql and bind parameters
    	$stmt = $this->conn->prepare("UPDATE person_address_rel SET PersonID = ".$personId.", AddressID = ".$addressId.
		" WHERE ID = ".$relId);	    	
     	
    	$stmt->execute();
    	
    	return $relId;
	}
}
