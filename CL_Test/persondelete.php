<?php
namespace CL_Test\V1;
class PersonDelete 
{
	private $conn;
	public function __construct($conn)
	{
        $this->conn = $conn;
	}	
	
	public function deletePerson($personId)
	{
		try
		{
			// prepare Person sql and bind parameters
	    	$stmt = $this->conn->prepare("UPDATE person_tbl SET Active = 0 WHERE ID = ".$personId);
	    	$stmt->execute();
		}
		catch(PDOException $e)
		{
  			return $e->getMessage();
		}
     	return true;
	}
}
