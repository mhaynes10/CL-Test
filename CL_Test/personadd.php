<?php
namespace CL_Test\V1;
class PersonAdd extends DbManager
{
	private $conn;	
	public function __construct($conn)
	{
        $this->conn = $conn;	
	}	
	
	public function insertPerson($person)
	{
		// prepare Person sql and bind parameters
    	$stmt = $this->conn->prepare("INSERT INTO person_tbl (First, Middle, Last, Active)
    	VALUES (:first, :middle, :last, :active)");
    	$stmt->bindParam(':first', $first);
    	$stmt->bindParam(':middle', $middle);
    	$stmt->bindParam(':last', $last);
    	$stmt->bindParam(':active', $personActive);
    	
    	// insert Person row
    	$first = $person->getFirst();
    	$middle = $person->getMiddle();
    	$last = $person->getLast();
    	$personActive = $person->isActive();
    	
    	$stmt->execute();

    	// fetch new ID
    	$lastPersonId = $this->conn->lastInsertId();

     	return $lastPersonId;
	}
}
