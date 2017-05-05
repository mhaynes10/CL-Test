<?php
namespace CL_Test\V1;
class AddressUpdate
{
	private $conn;	
	public function __construct($conn)
	{
        $this->conn = $conn;	
	}	
	
	public function updateAddress($address)
	{
		// prepare Address sql and bind parameters
    	$stmt = $this->conn->prepare("UPDATE address_tbl SET Addr1 = '".$address->getAddr1()."', Addr2 = '".$address->getAddr2()."', Addr3 = '".$address->getAddr3().
    	"', City = '".$address->getCity()."', State = '".$address->getState()."', StateAbbr = '".$address->getStateAbbr()."', ZipCode = '".$address->getZipCode().
    	"', ZipCode4 = '".$address->getZipCode4()."', Active = ".$address->isActive()." WHERE ID = ".$address->getId());
    	
		$stmt->execute();
		
     	return $address->getId();
	}
}
