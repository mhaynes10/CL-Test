<?php
namespace CL_Test\V1;
class AddressDelete extends DbManager
{
	private $conn;
	public function __construct($conn)
	{
        $this->conn = $conn;
	}	
	
	public function deleteAddress($addressId)
	{
		// prepare Person sql and bind parameters
    	$stmt = $this->conn->prepare("UPDATE address_tbl SET Active = 0 WHERE ID = ".$addressId);
    	$stmt->execute();

     	return true;
	}
}
