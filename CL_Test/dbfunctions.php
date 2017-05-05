<?php
namespace CL_Test\V1;
include 'personadd.php';
include 'personupdate.php';
include 'persondelete.php';
include 'addressadd.php';
include 'addressupdate.php';
include 'addressdelete.php';
include 'relationadd.php';
include 'relationupdate.php';
include 'relationdelete.php';

class dbFunctions
{
	private $conn;	
	private $personAdd;
	private $personUpdate;
	private $personDelete;
	private $addressAdd;
	private $addressUpdate;
	private $addressDelete;
	private $relationAdd;
	private $relationUpdate;
	private $relationDelete;	

	public function __construct($conn)
	{
      $this->conn = $conn;	
		$this->personAdd = new PersonAdd($conn);
		$this->personUpdate = new PersonUpdate($conn);
		$this->personDelete = new PersonDelete($conn);
		$this->addressAdd = new AddressAdd($conn);
		$this->addressUpdate = new AddressUpdate($conn);
		$this->addressDelete = new AddressDelete($conn);
		$this->relationAdd = new RelationAdd($conn);
		$this->relationUpdate = new RelationUpdate($conn);	
		$this->relationDelete = new RelationDelete($conn);	
	}	
	
	public function dbInsert($person, $address, $search)
	{
		$person->setId(0);
		$address->setId(0);		
		
		$personRows = $search->fetchPerson($person);
		if(count($personRows) > 0)
		{
			$foundPerson = new Person($personRows[0]["First"], $personRows[0]["Middle"], $personRows[0]["Last"], $personRows[0]["Active"]);			
			$foundPerson->setId($personRows[0]["ID"]);			
			if($foundPerson->isActive() == false) 
			{
				$foundPerson->setActive(true);
				$this->personUpdate->updatePerson($foundPerson);
   		}
			$personId = $foundPerson->getId();
		} 
		else 
		{		
		   $personId = $this->personAdd->insertPerson($person);
		}
		$testActive = false;
		$addressRows = $search->fetchAddress($address, $testActive);
		if(count($addressRows) > 0) 
		{
			$foundAddress = new Address($addressRows[0]["Addr1"], $addressRows[0]["Addr2"], $addressRows[0]["Addr3"], $addressRows[0]["City"],
			                           $addressRows[0]["State"], $addressRows[0]["StateAbbr"], $addressRows[0]["ZipCode"], $addressRows[0]["ZipCode4"],
			                           $addressRows[0]["Active"]);			
			$foundAddress->setId($addressRows[0]["ID"]);	
					
			if($foundAddress->isActive() == false) 
			{
				$foundAddress->setActive(true);
				$this->addressUpdate->updateAddress($foundAddress);
   		}
			$addressId = $foundAddress->getId();
		} 
		else 
		{	
		   $addressId = $this->addressAdd->insertAddress($address);
		}
		
		$relationRows = $search->fetchRelation($personId, $addressId);
		if(count($relationRows) > 0) 
		{
			$relationIdFound = $relationRows[0]['ID'];
			if($relationRows[0]['Active'] == false) 
			{
				$this->relationUpdate->updateRelation($relationIdFound, $personId, $addressId, true);
   		}
			$relationId = $relationIdFound;
		} 
		else 
		{	
			$relationId = $this->relationAdd->insertRelation($personId, $addressId);
		}
		
		$ids = array($personId, $addressId, $relationId);
		return $ids;
	}

	public function dbUpdate($relId, $person, $address, $search, $single)
	{
		echo "dbUpdate says single = ".$single;
		$updateRelNeeded = false;
		$result = $search->fetchPersonAddressIdsByRelation($relId);
		$person->setId($result[0]['PersonID']);
		$address->setId($result[0]['AddressID']);
		
		$addressId = $address->getId();
		$relationId = $relId;
				
		$personRows = $search->fetchPerson($person);
		//If a person matching the updated person is found
		if(count($personRows) > 0)
		{
			$ids = array(0, $addressId, $relationId);
			return $ids;		
		} 
		else 
		{		
		   $personId = $this->personUpdate->updatePerson($person);
		}
		
		//If an address matching the updated address is found
		$testActive = true;
		$addressRows = $search->fetchAddress($address,$testActive);
		if(count($addressRows) > 0) 
		{
			$this->relationDelete->deleteRelation($relationId);	
			
			$personCountArray = $search->fetchPersonCount($addressId);
         $personCount = $personCountArray[0]['count(*)'];
         if($personCount == 0) 
         {
         	$this->addressDelete->deleteAddress($addressId);
         }
			$ids = $this->dbInsert($person, $address, $search);
			$personId = $ids[0];
			$addressId = $ids[1];
			$relationId = $ids[2];
		} 
		else 
		{	
			if($single == 1) 
			{
				$this->relationDelete->deleteRelation($relationId);
				$ids = $this->dbInsert($person, $address, $search);
				$personId = $ids[0];
				$addressId = $ids[1];
				$relationId = $ids[2];
			}
			else 
			{
		     $addressId = $this->addressUpdate->updateAddress($address);
			}
		}
		
		$ids = array($personId, $addressId, $relationId);
		return $ids;
	}
	
	public function dbDelete($relId, $search)
	{
		$result = $search->fetchPersonAddressIdsByRelation($relId);
		$personId = $result[0]['PersonID'];
		$addressId = $result[0]['AddressID'];
		$relationId = $relId;

		// Prep
		$deletedAddress = false;
		$deletedPerson = false;

		// Delete this relation (It's not meant to last...)
		$deletedRelation = $this->relationDelete->deleteRelation($relationId);		
		
		// If no other person attached to address, delete address
		$persons = $search->fetchPersonCount($addressId);
		if($persons[0]['count(*)'] == 0) 
   	{
		   $deletedAddress = $this->addressDelete->deleteAddress($addressId);		
		}

		// If no other address attached to person, delete person
		$addresses = $search->fetchAddressCount($personId);
		if($addresses[0]['count(*)'] == 0) 
   	{
		   $deletedPerson = $this->personDelete->deletePerson($personId);		
		}

		if($deletedAddress) 
		{
			return null;
		}	
		else 
		{
			$addressResult = $search->fetchAddressById($addressId);
			$address = $addressResult[0];
			return $address;
		}
	}

}
