<?php
namespace CL_Test\V1;
include 'personadd.php';
include 'personUpdate.php';
include 'addressadd.php';
include 'addressupdate.php';
include 'relationadd.php';
include 'relationupdate.php';

class dbFunctions
{
	private $conn;	
	private $personAdd;
	private $personUpdate;
	private $addressAdd;
	private $addressUpdate;
	private $relationAdd;
	private $relationUpdate;	

	public function __construct($conn)
	{
      $this->conn = $conn;	
		$this->personAdd = new PersonAdd($conn);
		$this->personUpdate = new PersonUpdate($conn);
		$this->addressAdd = new AddressAdd($conn);
		$this->addressUpdate = new AddressUpdate($conn);
		$this->relationAdd = new RelationAdd($conn);
		$this->relationUpdate = new RelationUpdate($conn);	
	}	
	
	public function dbInsert($person, $address, $search)
	{
		$personRows = $search->fetchPerson($person);
		if(count($personRows) > 0)
		{
			$personId = $personRows[0]['ID'];
		} 
		else 
		{		
		   $personId = $this->personAdd->insertPerson($person);
		}
		
		$addressRows = $search->fetchAddress($address);
		if(count($addressRows) > 0) 
		{
			$addressId = $addressRows[0]['ID'];
		} 
		else 
		{	
		   $addressId = $this->addressAdd->insertAddress($address);
		}
		
		$relationRows = $search->fetchRelation($personId, $addressId);
		if(count($relationRows) > 0) 
		{
			$relationId = $relationRows[0]['ID'];
		} 
		else 
		{	
			$relationId = $this->relationAdd->insertRelation($personId, $addressId);
		}
		
		$ids = array($personId, $addressId, $relationId);
		return $ids;
	}

	public function dbUpdate($relId, $person, $address, $search)
	{
		$updateRelNeeded = false;
		$result = $search->fetchPersonAddressIdsByRelation($relId);
		$person->setId($result[0]['PersonID']);
		$address->setId($result[0]['AddressID']);
		$relationId = $relId;
				
		$personRows = $search->fetchPerson($person);
		if(count($personRows) > 0)
		{
			//TODO:Delete original person (find with $person->getId())			
			$personId = $personRows[0]['ID'];
		} 
		else 
		{		
		   $personId = $this->personUpdate->updatePerson($person);
		}
		$updateRelNeeded = $personId != $person->getId();
		
		$addressRows = $search->fetchAddress($address);
		if(count($addressRows) > 0) 
		{
			//TODO:Delete original address (find with $address->getId())
			$addressId = $addressRows[0]['ID'];
		} 
		else 
		{	
		   $addressId = $this->addressUpdate->updateAddress($address);
		}
		$updateRelNeeded = $updateRelNeeded || $addressId != $address->getId();
		
		if($updateRelNeeded) 
		{		
		  $relationRows = $search->fetchRelation($personId, $addressId);
		  if(count($relationRows) > 0) 
		  {
			  $relationId = $relationRows[0]['ID'];
			  //TODO:Delete original relation (find with $relId)
		  } 
		  else 
		  {	
			  $relationId = $this->relationUpdate->updateRelation($relId, $personId, $addressId);
		  }
		}
		$ids = array($personId, $addressId, $relationId);
		return $ids;
	}

}
