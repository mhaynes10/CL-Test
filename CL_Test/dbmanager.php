<?php
namespace CL_Test\V1;
use \PDO;

class DbManager
{
	private $servername = "localhost";
	private $username = "guest";
	private $password = "guest123";
	private $dbname = "cl_test_db";
	private $conn;

	public function dbConnect() 
	{
		$servername = $this->servername;
		$username = $this->username;
		$password = $this->password;
		$dbname = $this->dbname;
		
    	try 
    	{
			// Create connection
   		$this->conn = new PDO("mysql:host=".$servername.";dbname=".$dbname, $username, $password);

    		// set the PDO error mode to exception
    		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	}
		catch(PDOException $e)
    	{
    		return $e->getMessage();
    	}
    	return $this->conn;
	}
	
	public function dbDisconnect() 
	{
		$this->conn = null;
	}
} 
