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

//DbFunctions manages basic add, update, and delete functions
//Objects are instantiated to carry out these functions
//The $conn connection from a DbManager instance is passed into the constructor
//to be passed on to the various Add..., Update..., and Delete... objects.
class DbFunctions     
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
	//Insert
	public function dbInsert($person, $address, $search)
	{
		//IDs are set to 0, since the objects don't have a home in the database yet
		$person->setId(0);
		$address->setId(0);		

		//First we use $search to find any Person records matching the $person to be added
		$testActive = false;		
		$personRows = $search->fetchPerson($person, $testActive);

		//If found, we instantiate a new Person as $foundPerson, constructed with the found data and set the ID accordingly
		if(count($personRows) > 0)
		{
			$foundPerson = new Person($personRows[0]["First"], $personRows[0]["Middle"], $personRows[0]["Last"], $personRows[0]["Active"]);			
			$foundPerson->setId($personRows[0]["ID"]);			
			//If the record found was previously "deleted," we "undelete" by setting Active to true and updating the record			
			if($foundPerson->isActive() == false) 
			{
				$foundPerson->setActive(true);
				$this->personUpdate->updatePerson($foundPerson);
   		}
   		//Otherwise, we can skip that part, and in either case, the "added" Person simply "assumes the ID" of the 
   		//already existing person (We avoid creating a duplicate record and thus avoid the need of throwing an error)
			$personId = $foundPerson->getId();
		} 
		else 
		//No duplicates found. Insert new Person into the database		
		{		
		   $personId = $this->personAdd->insertPerson($person);
		}
		//Now we do the same for Address:
		//The commonalities between WHERE clauses of the various address queries in Search were numerous enough to create a separate 
		//function for it.
		//So here when we request a search for duplicate data, we pass in $testActive as false, because we don't want to restrict
		//the search to only active records.
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

		//Last but not least is the relation linking the Person with the Address; same basic scenario...

		//Note: I didn't make a class for Relation. I probably should have..
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

	//Update

   //At the expense of over thinking this test project, it occurred to me that in a real life scenario,
   //An Address change for one Person wouldn't necessarily imply an Address for other Persons attached
   //to that Address, thus I let the user decide to "change for all persons or change for just this one."
   //The $single parameter contains the user's decision 
	public function dbUpdate($relId, $person, $address, $search, $single)
	{
		$updateRelNeeded = false; //We first assume the relation will not need to be changed
		$result = $search->fetchPersonAddressIdsByRelation($relId);
		$person->setId($result[0]['PersonID']);
		$address->setId($result[0]['AddressID']);
		
      $personId = $person->getId();	
		$addressId = $address->getId();
		$relationId = $relId;
		
		$testActive = false;		
		$personRows = $search->fetchPerson($person, $testActive);

		if(count($personRows) > 0)
      {
          //If found, we instantiate a new Person as $foundPerson, constructed with the found data and set the ID accordingly
	       $foundPerson = new Person($personRows[0]["First"], $personRows[0]["Middle"], $personRows[0]["Last"], $personRows[0]["Active"]);			
			 $foundPerson->setId($personRows[0]["ID"]);			
			 //If the record found was previously "deleted," we "undelete" by setting Active to true and updating the record			
			 if($foundPerson->isActive() == false) 
			 {
			     $foundPerson->setActive(true);
			     $this->personUpdate->updatePerson($foundPerson);
 			     $this->personDelete->deletePerson($personId);
 				  $personId = $foundPerson->getId();
   		 }
   		 else
		    {
				  //If an Active person matching the updated person is found...
				  //I decided to throw an error back to the user if the updated Person data
				  //matched an already existing Active record. So I pass back a 0 as the Person ID to indicate no update
				  //due to duplicate record. 

			     $ids = array(0, $addressId, $relationId);
			     return $ids;		
		    } 
		 }
		 //Otherwise
		 else
		 {		
		    $personId = $this->personUpdate->updatePerson($person);
		 }
		
		//If an address matching the updated address is found...
		//I decided to make accommodations for a duplicate Address similar to what I did with dbInsert,
		
		$testActive = false;
		$addressRows = $search->fetchAddress($address,$testActive);
		if(count($addressRows) > 0) 
		{
			$this->relationDelete->deleteRelation($relationId);	
			
			//Removing the relation isn't enough. Now we have to see if this particular address is attached to another Person.
			//If not, we'll remove the address.
			$personCountArray = $search->fetchPersonCount($addressId);
         $personCount = $personCountArray[0]['count(*)'];
 
         //If found, we instantiate a new Address as $foundAddress, constructed with the found data and set the ID accordingly
			$foundAddress = new Address($addressRows[0]["Addr1"], $addressRows[0]["Addr2"], $addressRows[0]["Addr3"], $addressRows[0]["City"],
			                           $addressRows[0]["State"], $addressRows[0]["StateAbbr"], $addressRows[0]["ZipCode"], $addressRows[0]["ZipCode4"],
			                           $addressRows[0]["Active"]);			
			$foundAddress->setId($addressRows[0]["ID"]);	
			
			//No other person attached to this address? We can delete it
          if($personCount == 0 || $single == 0) 
          {
          	   $this->addressDelete->deleteAddress($addressId);
          }
			 
			 //If the record found was previously "deleted," we "undelete" by setting Active to true and updating the record			
			 if($foundAddress->isActive() == false) 
			 {
			     $foundAddress->setActive(true);
			     $this->addressUpdate->updateAddress($foundAddress);
   		 }
			 if($single == 0) //The address change is not reserved for one person only, so we update it's ID in the relation table 
			 {
			 	  $foundAddressId = $foundAddress->getId();
			 	  $this->relationUpdate->updateAddressRelation($addressId, $foundAddressId);
			 }
			 $addressId = $foundAddress->getId();
         
          //We'll let dbInsert handle it from here, since it handles the duplicate issue by creating a new relation
          //and using the existing IDs of the Person duplicate and/or Address duplicate.
			 $ids = $this->dbInsert($person, $address, $search);
			
			// (We'll be returning these)
			$personId = $ids[0];
			$addressId = $ids[1];
			$relationId = $ids[2];
		} 
		else //No duplicate addresses
		{	
			if($single == 1) 
			{
				//If we're here, it means there were multiple Persons attached to the one Address and the user elected 
				//to preserve the original Address for the other Persons (or "change" just the single address).
				//So, we'll simply remove the relation and insert the updated Address using dbInsert().  
				$this->relationDelete->deleteRelation($relationId);
				$ids = $this->dbInsert($person, $address, $search);
				$personId = $ids[0];
				$addressId = $ids[1];
				$relationId = $ids[2];
			}
			else 
			{
				//Otherwise, it's just a simple update
		     $addressId = $this->addressUpdate->updateAddress($address);
			}
		}
		
		$ids = array($personId, $addressId, $relationId);
		return $ids;
	}


	//Delete	
	public function dbDelete($relId, $search)
	{
		//Use the relation ID to find exactly what we want to "delete"
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
			//If an address is still around (still attached to another person), return it for a result query
			$addressResult = $search->fetchAddressById($addressId);
			$address = $addressResult[0];
			return $address;
		}
	}
}
