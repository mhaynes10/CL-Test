<?php
namespace CL_Test\V1;
use \PDO;

class DbManager
{
	private $servername = "localhost";
	private $username = "guest";
	private $password = "guest123";
	private $dbname = "cl_test_db";
	private $conn;

	public function dbConnect() 
	{
		$servername = $this->servername;
		$username = $this->username;
		$password = $this->password;
		$dbname = $this->dbname;
		
    	try 
    	{
			// Create connection
   		$this->conn = new PDO("mysql:host=".$servername.";dbname=".$dbname, $username, $password);

    		// set the PDO error mode to exception
    		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	}
		catch(PDOException $e)
    	{
    		return $e->getMessage();
    	}
    	return 0;
	}
	
	public function dbDisconnect() 
	{
		$this->conn = null;
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

	public function insertAddress($address)
	{
		// prepare Address sql and bind parameters
    	$stmt = $this->conn->prepare("INSERT INTO address_tbl (Addr1, Addr2, Addr3, City, State, StateAbbr, ZipCode, ZipCode4, Active)
    	VALUES (:addr1, :addr2, :addr3, :city, :state, :stateAbbr, :zipCode, :zipCode4, :active)");
    	$stmt->bindParam(':addr1', $addr1);
    	$stmt->bindParam(':addr2', $addr2);
    	$stmt->bindParam(':addr3', $addr3);
    	$stmt->bindParam(':city', $city);
    	$stmt->bindParam(':state', $state);
    	$stmt->bindParam(':stateAbbr', $stateAbbr);
    	$stmt->bindParam(':zipCode', $zipCode);
    	$stmt->bindParam(':zipCode4', $zipCode4);
    	$stmt->bindParam(':active', $addrActive);
    	
    	// insert Address row
    	$addr1 = $address->getAddr1();
    	$addr2 = $address->getAddr2();
    	$addr3 = $address->getAddr3();
    	$city = $address->getCity();
    	$state = $address->getState();
    	$stateAbbr = $address->getStateAbbr();
    	$zipCode = $address->getZipCode();
    	$zipCode4 = $address->getZipCode4();
    	$addrActive = $address->isActive();

		$stmt->execute();
		
		// fetch new ID
		$lastAddressId = $this->conn->lastInsertId();

     	return $lastAddressId;
	}
	
	public function insertRelation($personId, $addressId)
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
    	
    	// fetch new ID
    	$lastRelationId = $this->conn->lastInsertId();

    	return $lastRelationId;
	}
	
	public function dbInsert($person, $address)
	{
		$personId = $this->insertPerson($person);
		$addressId = $this->insertAddress($address);
		$relationId = $this->insertRelation($personId, $addressId);
		$ids = array($personId, $addressId, $relationId);
		return $ids;
	}

} 
