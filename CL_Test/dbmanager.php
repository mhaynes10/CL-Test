<?php
namespace CL_Test\V1;
use \PDO;

class DbManager //Two basic functions: connect and disconnect
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
    		return $e->getMessage(); //In testing, I never managed to catch PDO errors. PDO errors I generated were all "Fatal" and killed
   	}									 //the process in it's tracks. Not a road block necessarily, but something I'd like to get a  
    	return $this->conn;			 //better understanding of.
	}
	
	public function dbDisconnect() 
	{
		$this->conn = null;
	}
} 
