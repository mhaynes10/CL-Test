<?php
namespace CL_Test\V1;
class RelationDelete 
{
	private $conn;	
	public function __construct($conn)
	{
        $this->conn = $conn;	
	}	
	
	public function deleteRelation($relId)
	{
		// prepare Person-Address Relation sql and bind parameters
    	$stmt = $this->conn->prepare("UPDATE person_address_rel SET Active = 0 WHERE ID = ".$relId);	    	
     	
    	$stmt->execute();
    	
    	return true;
	}
}
