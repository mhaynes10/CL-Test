<?php
namespace CL_Test\V1;
use \PDO;
use \RecursiveArrayIterator;
use \RecursiveIteratorIterator;

	class TableRows extends RecursiveIteratorIterator { 
    	function __construct($it) { 
        parent::__construct($it, self::LEAVES_ONLY); 
    	}

    	function current() {
        return "<td style='width:800px;border:1px solid black;'>" . parent::current(). "</td>";
    	}

    	function beginChildren() { 
        echo "<tr>"; 
    	} 

    	function endChildren() { 
        echo "</tr>" . "\n";
    	} 
	} 



class AddressFetch
{
	private $conn;	
	public function __construct($conn)
	{
        $this->conn = $conn;	
	}	
	
	
	public function fetchAddress($address)
	{
	echo "<table style='border: solid 1px black;'>";
	echo "<tr><th>Id</th><th>Addr1</th><th>Addr2</th><th>Addr3</th><th>City</th><th>State</th><th>StateAbbr</th><th>ZipCode</th><th>ZipCode4</th><th>Active</th></tr>";


		// prepare Address sql and bind parameters
    	$stmt = $this->conn->prepare("SELECT ID ,Addr1, Addr2, Addr3, City, State, StateAbbr, ZipCode, ZipCode4, Active from address_tbl");
		$stmt->execute();
		
		
	// set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) { 
        echo $v;
    }
		
	echo "</table>";	
		

	}
}
?>