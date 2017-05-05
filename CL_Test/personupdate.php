<?php
namespace CL_Test\V1;
class PersonUpdate extends DbManager
{
	private $conn;	
	public function __construct($conn)
	{
        $this->conn = $conn;	
	}	
	
	public function updatePerson($person)
	{
		// prepare Person sql and bind parameters
    	$stmt = $this->conn->prepare("UPDATE person_tbl SET First = '".$person->getFirst()."', Middle = '".$person->getMiddle().
    	"', Last = '".$person->getLast()."', Active = ".$person->isActive().
    	" WHERE ID = ".$person->getID());
    	
    	$stmt->execute();

     	return $person->getID();
	}
}
