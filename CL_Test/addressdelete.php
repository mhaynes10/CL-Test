<?php
namespace CL_Test\V1;
class AddressDelete 
{
	private $conn;
	public function __construct($conn)
	{
        $this->conn = $conn;
	}	
	
	public function deleteAddress($addressId)
	{
		try
		{		
			// prepare Person sql
    		$stmt = $this->conn->prepare("UPDATE address_tbl SET Active = 0 WHERE ID = ".$addressId);
    		$stmt->execute();
		}
		catch(PDOException $e)
  		{
  			return $e->getMessage();
  		}
     	return true;
	}
}
