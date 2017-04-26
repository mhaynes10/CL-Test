<?php

class Person{
	private $first;
	private $middle;
	private $last;
	private $active;
	private $id;
	
	public function __construct($first, $middle, $last, $active){
		$this->setFirst($first);
		$this->setMiddle($middle);
		$this->setLast($last);
		$this->setActive($active);
	}
	
	public function setFirst($first){
		$this->first = $first;	
	}
	public function setMiddle($middle){
		$this->middle = $middle;	
	}
	public function setLast($last){
		$this->last = $last;	
	}
	public function setActive($active){
		$this->active = $active;	
	}
	public function setId($id){
		$this->id = $id;	
	}	
	
	public function getFirst(){
		return $this->first;	
	}
	public function getMiddle(){
		return $this->middle;	
	}
	public function getLast(){
		return $this->last;	
	}
	public function isActive(){
		return $this->active;	
	}	
	public function getId(){
		return $this->id;	
	}
}

class Address{
   private $addr1;
   private $addr2;
   private $addr3;
   private $city;
   private $state;
   private $stateAbbr;
   private $zipCode;
   private $zipCode4;
	private $active;
	private $id;
	
	public function __construct($addr1, $addr2, $addr3, $city, $state, $stateAbbr, $zipCode, $zipCode4, $active){
		$this->setAddr1($addr1);
		$this->setAddr2($addr2);
		$this->setAddr3($addr3);
		$this->setCity($city);
		$this->setState($state);
		$this->setStateAbbr($stateAbbr);
		$this->setZipCode($zipCode);
		$this->setZipCode4($zipCode4);
		$this->setActive($active);
	}

	public function setAddr1($addr1){
		$this->addr1 = $addr1;	
	}
	public function setAddr2($addr2){
		$this->addr2 = $addr2;	
	}
	public function setAddr3($addr3){
		$this->addr3 = $addr3;	
	}
	public function setCity($city){
		$this->city = $city;	
	}
	public function setState($state){
		$this->state = $state;	
	}
	public function setStateAbbr($stateAbbr){
		$this->stateAbbr = $stateAbbr;	
	}
	public function setZipCode($zipCode){
		$this->zipCode = $zipCode;	
	}
	public function setZipCode4($zipCode4){
		$this->zipCode4 = $zipCode4;	
	}
	public function setActive($active){
		$this->active = $active;	
	}	
	public function setId($id){
		$this->id = $id;	
	}	


	public function getAddr1(){
		return $this->addr1;	
	}
	public function getAddr2(){
		return $this->addr2;	
	}
	public function getAddr3(){
		return $this->addr3;	
	}
	public function getCity(){
		return $this->city;	
	}
	public function getState(){
		return $this->state;	
	}
	public function getStateAbbr(){
		return $this->stateAbbr;	
	}
	public function getZipCode(){
		return $this->zipCode;	
	}
	public function getZipCode4(){
		return $this->zipCode4;	
	}
	public function isActive(){
		return $this->active;	
	}	
	public function getId(){
		return $this->id;	
	}
}

class DbManager{
	private $servername = "localhost";
	private $username = "guest";
	private $password = "guest123";
	private $dbname = "cl_test_db";
	private $conn;

	public function db_connect() {
		// Create connection
		$servername = $this->servername;
		$username = $this->username;
		$password = $this->password;
		$dbname = $this->dbname;
		
    	try 
    	{
    		$this->conn = new PDO("mysql:host=".$servername.";dbname=".$dbname, $username, $password);
    		// set the PDO error mode to exception
    		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	}
		catch(PDOException $e)
    	{
    		echo "Error: " . $e->getMessage();
    	}
	}
	
	public function db_disconnect() {
		$this->conn = null;
	}
		
	
	public function insertPerson($person){
		
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
    	$lastPersonId = $this->conn->lastInsertId();

     	return $lastPersonId;
	}

	public function insertAddress($address){
		
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
		$lastAddressId = $this->conn->lastInsertId();

     	return $lastAddressId;
	}
	
	public function insertRelation($personId, $addressId){
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
    	$lastRelationId = $this->conn->lastInsertId();

    	return $lastRelationId;
	}
	
	public function db_insert($person, $address){
		
		$personId = $this->insertPerson($person);
		$addressId = $this->insertAddress($address);
		$relationId = $this->insertRelation($personId, $addressId);
		$ids = array($personId, $addressId, $relationId);
		return $ids;
	}

} 

function unitTestInsert() {
  $person = new Person("Maggie"," ", "Haynes", true);
  $address = new Address("3123 W. Glenwood Street", "Dog House", "Outside", "Springfield", "Missouri", "MO", "65807", " ", true);
  $dbMgr = new DbManager;

  $dbMgr->db_connect();
 
  $ids = $dbMgr->db_insert($person,$address);
  $person->setId($ids[0]);
  $address->setId($ids[1]);
  
  $person2 = new Person("Elvira"," ", "Haynes", true);

  $person2->setId($dbMgr->insertPerson($person2));
  $dbMgr->insertRelation($person2->getId(), $address->getId());
  
  $dbMgr->db_disconnect();
  
  echo "unitTestInsert Success!";
}

unitTestInsert();

?>