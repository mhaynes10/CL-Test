<?php
namespace CL_Test\V1;
use \PDO;

class Search 
{
	private $conn;	
	public function __construct($conn)
	{
        $this->conn = $conn;	
	}	
	
	function isEmpty($string)
	{
		if($string == null || $string == "")
		{
			return true; 		
		}
		else 
		{
			return false;
		}
	}	
	
	
	function buildWhereClauseA($address,$ignoreCond,$testActive) // Builds a WHERE clause using only the address fields which are NOT empty.  
	{                                                // If $ignoreCond = true, all fields are used regardless  
		$conj = ""; 		                             
		$whereClause = "WHERE ";
		if ($ignoreCond || !$this->isEmpty($address->getAddr1())) 
		{
			$whereClause = $whereClause."Addr1 = "."'".$address->getAddr1()."'";
			$conj = " AND ";
		}
		if ($ignoreCond || !$this->isEmpty($address->getAddr2())) 
		{
			$whereClause = $whereClause.$conj."Addr2 = "."'".$address->getAddr2()."'";
			$conj = " AND ";
		}
		if ($ignoreCond || !$this->isEmpty($address->getAddr3())) 
		{
			$whereClause = $whereClause.$conj."Addr3 = "."'".$address->getAddr3()."'";
			$conj = " AND ";
		}
		if ($ignoreCond || !$this->isEmpty($address->getCity())) 
		{
			$whereClause = $whereClause.$conj."City = "."'".$address->getCity()."'";
			$conj = " AND ";
		}
		if ($ignoreCond || !$this->isEmpty($address->getState())) 
		{
			$whereClause = $whereClause.$conj."State = "."'".$address->getState()."'";
			$conj = " AND ";
		}
		if ($ignoreCond || !$this->isEmpty($address->getStateAbbr())) 
		{
			$whereClause = $whereClause.$conj."StateAbbr = "."'".$address->getStateAbbr()."'";
			$conj = " AND ";
		}
		if ($ignoreCond || !$this->isEmpty($address->getZipCode())) 
		{
			$whereClause = $whereClause.$conj."ZipCode = "."'".$address->getZipCode()."'";
			$conj = " AND ";
		}
		if ($ignoreCond || !$this->isEmpty($address->getZipCode4())) 
		{
			$whereClause = $whereClause.$conj."ZipCode4 = "."'".$address->getZipCode4()."'";
		}
		if($testActive) 
		{		
		   $whereClause = $whereClause.$conj."address_tbl.Active = 1"; //Allows to pull all active records
		}																				   //when all other field are empty. 
		return $whereClause;
	}	

	function runQuery($sqlQuery)
	{
		// prepare sql     	
    	$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		
   	// set the resulting array to associative
      $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
      $rows = $stmt->fetchAll();
      return $rows; 
	}

	public function fetchAddress($address,$testActive)
	{	
		// This query is used to check for a duplicate address before insert 	

		// Build WHERE Clause
		$ignoreCond = true;			
		$whereClause = $this->buildWhereClauseA($address, $ignoreCond, $testActive);
		$whereClause = $whereClause." AND ID <> ".$address->getId(); 

		// Build query string
    	$sqlQuery = "SELECT ID ,Addr1, Addr2, Addr3, City, State, StateAbbr, ZipCode, ZipCode4, Active from address_tbl ".$whereClause;
    	
    	// Run query and return result
		return $this->runQuery($sqlQuery);  
    }
    
   public function fetchPerson($person)
	{		
		// This query is used to check for a duplicate person before insert 	

		//Build WHERE clause		
		$whereClause = "WHERE ID <> ".$person->getId()." AND First = "."'".$person->getFirst()."' AND Middle = "."'".$person->getMiddle()."' AND Last = "."'".$person->getLast()."'"; 
		
		// Build query string
    	$sqlQuery = "SELECT ID ,First, Middle, Last, Active from person_tbl ".$whereClause;
		
		// Run query and return result
      return $this->runQuery($sqlQuery);  
    }
    
	public function fetchPersonAddress($address)
	{	
		// This is the main query for typical usage		
		
		//Build WHERE clause			
		$ignoreCond = false;	
		$whereClause = $this->buildWhereClauseA($address, $ignoreCond, true);
		$whereClause = $whereClause." AND person_tbl.Active = 1 AND person_address_rel.Active = 1";
		
		// Build query string
		$sqlQuery = "SELECT person_address_rel.ID as RelID, person_tbl.ID, Last, First, Middle, person_tbl.Active, person_address_rel.PersonID, person_address_rel.AddressID, address_tbl.ID ,Addr1, Addr2, Addr3, City, State, StateAbbr, ZipCode, ZipCode4, address_tbl.Active "
    	."FROM address_tbl JOIN person_address_rel ON address_tbl.ID = person_address_rel.AddressID JOIN person_tbl ON person_address_rel.PersonID = person_tbl.ID "
    	.$whereClause." ORDER BY Last, First";	

      // Run query and return result
      return $this->runQuery($sqlQuery); 
    }  
   
    public function fetchPersonAddressByRelation($relationId)
    {		
		// This query is used to check for a duplicate relation before insert 	
		
		//Build WHERE clause
		$whereClause = "WHERE person_address_rel.ID = ".$relationId." AND address_tbl.Active = 1 AND person_tbl.Active = 1 AND person_address_rel.Active = 1";
		
		// Build query string
		$sqlQuery = "SELECT person_address_rel.ID as RelID, person_tbl.ID, Last, First, Middle, person_tbl.Active, person_address_rel.PersonID, person_address_rel.AddressID, address_tbl.ID ,Addr1, Addr2, Addr3, City, State, StateAbbr, ZipCode, ZipCode4, address_tbl.Active "    	
    	."FROM address_tbl JOIN person_address_rel ON address_tbl.ID = person_address_rel.AddressID JOIN person_tbl ON person_address_rel.PersonID = person_tbl.ID "
    	.$whereClause;	
		
      // Run query and return result
      return $this->runQuery($sqlQuery); 
    }
    
    public function fetchPersonAddressIdsByRelation($relationId)
    {		
		// This query is used to check for a duplicate relation before insert 	
		
		//Build WHERE clause
		$whereClause = "WHERE ID = ".$relationId." AND Active = 1";
		
		// Build query string
		$sqlQuery = "SELECT PersonID, AddressID FROM person_address_rel ".$whereClause;	
		
      // Run query and return result
      return $this->runQuery($sqlQuery); 
    }
    
    public function fetchRelation($personId, $addressId)
	 {	
	 	// This query is used to retrieve the ID of the relation record 	
	 		
		// Build query string
    	$sqlQuery = "SELECT ID, Active from person_address_rel WHERE PersonID = ".$personId." AND AddressID = ".$addressId;
    	
    	// Run query and return result
      return $this->runQuery($sqlQuery); 
    }   
    
    public function fetchPersonCount($addressId)
	 {	
	 	// This query is used to retrieve the count of persons attached to an address 	
	 		
		// Build query string
    	$sqlQuery = "SELECT count(*) from person_address_rel WHERE AddressID = ".$addressId." AND Active = 1";
    	
    	// Run query and return result
      return $this->runQuery($sqlQuery); 
    }   

    public function fetchAddressCount($personId)
	 {	
	 	// This query is used to retrieve the count of addresses attached to a person 	
	 		
		// Build query string
    	$sqlQuery = "SELECT count(*) from person_address_rel WHERE PersonID = ".$personId." AND Active = 1";
    	
    	// Run query and return result
      return $this->runQuery($sqlQuery); 
    }   
    
  	public function fetchAddressById($addressId)
	{	
		// Simple search by ID		
		// Build query string
		$sqlQuery = "SELECT ID ,Addr1, Addr2, Addr3, City, State, StateAbbr, ZipCode, ZipCode4, Active "
    	."FROM address_tbl WHERE ID = ".$addressId;	

      // Run query and return result
      return $this->runQuery($sqlQuery); 
    }  

  	public function fetchPersonById($personId)
	{	
		// Simple search by ID		
		// Build query string
		$sqlQuery = "SELECT ID ,First, Middle, Last, Active "
    	."FROM person_tbl WHERE ID = ".$personId;	

      // Run query and return result
      return $this->runQuery($sqlQuery); 
    }  


}
?>