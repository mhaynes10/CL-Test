<?php
namespace CL_Test\V1;
use \PDO;

class AddressFetch
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
	
	function buildWhereClauseA($address)
	{
		$conj = ""; 		
		$whereClause = "WHERE ";
		if (!$this->isEmpty($address->getAddr1())) 
		{
			$whereClause = $whereClause."Addr1 = "."'".$address->getAddr1()."'";
			$conj = " AND ";
		}
		if (!$this->isEmpty($address->getAddr2())) 
		{
			$whereClause = $whereClause.$conj."Addr2 = "."'".$address->getAddr2()."'";
			$conj = " AND ";
		}
		if (!$this->isEmpty($address->getAddr3())) 
		{
			$whereClause = $whereClause.$conj."Addr3 = "."'".$address->getAddr3()."'";
			$conj = " AND ";
		}
		if (!$this->isEmpty($address->getCity())) 
		{
			$whereClause = $whereClause.$conj."City = "."'".$address->getCity()."'";
			$conj = " AND ";
		}
		if (!$this->isEmpty($address->getState())) 
		{
			$whereClause = $whereClause.$conj."State = "."'".$address->getState()."'";
			$conj = " AND ";
		}
		if (!$this->isEmpty($address->getStateAbbr())) 
		{
			$whereClause = $whereClause.$conj."StateAbbr = "."'".$address->getStateAbbr()."'";
			$conj = " AND ";
		}
		if (!$this->isEmpty($address->getZipCode())) 
		{
			$whereClause = $whereClause.$conj."ZipCode = "."'".$address->getZipCode()."'";
			$conj = " AND ";
		}
		if (!$this->isEmpty($address->getZipCode4())) 
		{
			$whereClause = $whereClause.$conj."ZipCode4 = "."'".$address->getZipCode4()."'";
		}
		$whereClause = $whereClause.$conj."address_tbl.Active = 1";

		return $whereClause;
	}	
	
	public function fetchAddress($address)
	{		
		$whereClause = $this->buildWhereClauseA($address);
		//Debug		
		echo 	$whereClause."<br/>";
		// prepare Address sql and bind parameters
    	$stmt = $this->conn->prepare("SELECT ID ,Addr1, Addr2, Addr3, City, State, StateAbbr, ZipCode, ZipCode4, Active from address_tbl "
    	.$whereClause);
    	
		$stmt->execute();
		
   	// set the resulting array to associative
      $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	      
      $rows = $stmt->fetchAll();
		//Debug		
		echo count($rows);

       return $rows; 
    }
    
   public function fetchPerson($person)
	{		
		$whereClause = "WHERE First = "."'".$person->getFirst()."' AND Middle = "."'".$person->getMiddle()."' AND Last = "."'".$person->getLast()."'"; 
		//Debug		
		echo 	$whereClause."<br/>";
		// prepare Address sql and bind parameters
    	$stmt = $this->conn->prepare("SELECT ID ,First, Middle, Last, Active from person_tbl "
    	.$whereClause);
    	
		$stmt->execute();
		
   	// set the resulting array to associative
      $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	      
      $rows = $stmt->fetchAll();
		//Debug		
		echo count($rows);

       return $rows; 
    }

    
	public function fetchPersonAddress($address)
	{		
		$whereClause = $this->buildWhereClauseA($address);
		$whereClause = $whereClause." AND person_tbl.Active = 1 AND person_address_rel.Active = 1";
		
		// prepare Address sql and bind parameters
		$sqlQuery = "SELECT person_tbl.ID, First, Middle, Last, person_tbl.Active, person_address_rel.PersonID, person_address_rel.AddressID, address_tbl.ID ,Addr1, Addr2, Addr3, City, State, StateAbbr, ZipCode, ZipCode4, address_tbl.Active "
    	."FROM address_tbl JOIN person_address_rel ON address_tbl.ID = person_address_rel.AddressID JOIN person_tbl ON person_address_rel.PersonID = person_tbl.ID "
    	.$whereClause;	
		//Debug
		echo 	$sqlQuery."<br/>";		
		
    	$stmt = $this->conn->prepare($sqlQuery);
    	
		$stmt->execute();
		
   	// set the resulting array to associative
      $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	      
      $rows = $stmt->fetchAll();
		//Debug
		echo count($rows);

       return $rows; 
    }    
    
}
?>