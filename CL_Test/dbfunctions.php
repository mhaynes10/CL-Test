<?php
namespace CL_Test\V1;
include 'personadd.php';
include 'addressadd.php';
include 'relationadd.php';

class dbFunctions
{
	private $conn;	
	private $personAdd;
	private $addressAdd;
	private $relationAdd;	

	public function __construct($conn)
	{
      $this->conn = $conn;	
		$this->personAdd = new PersonAdd($conn);
		$this->addressAdd = new AddressAdd($conn);
		$this->relationAdd = new RelationAdd($conn);	
	}	
	
	public function dbInsert($person, $address)
	{
		$personId = $this->personAdd->insertPerson($person);
		$addressId = $this->addressAdd->insertAddress($address);
		$relationId = $this->relationAdd->insertRelation($personId, $addressId);
		$ids = array($personId, $addressId, $relationId);
		return $ids;
	}
}
