<?php
namespace CL_Test\V1;
class AddressAdd
{
	private $conn;	
	public function __construct($conn)
	{
        $this->conn = $conn;	
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
}
