<?php
namespace CL_Test\V1;
use \RecursiveArrayIterator;
use \RecursiveIteratorIterator;

include_once 'dbmanager.php';
include_once 'person.php';
include_once 'address.php';
include_once 'dbfunctions.php';

include_once 'addressfetch.php';

class TableRows extends RecursiveIteratorIterator 
{ 
  	function __construct($it) 
  	{ 
   	parent::__construct($it, self::LEAVES_ONLY); 
  	}

   function current() 
   {
      return "<td style='width:800px;border:1px solid black;'>" . parent::current(). "</td>";
   }

   function beginChildren() 
   { 
      echo "<tr><td style='width:800px;border:1px solid black;'><button type='button'>Edit</button><button type='button'>Delete</button></td>"; 
   } 

   function endChildren() 
   { 
      echo "</tr>" . "\n";
   } 
} 

function unitTestInsert() 
{
  $person = new Person("Maggie","Paws", "Haynes", true);
  $person2 = new Person("Elvira","Bad-Breath", "Haynes", true);
  $address = new Address("3123 W. Glenwood Street", "Dog House", "Outside", "Springfield", "Missouri", "MO", "65807", " ", true);
  $address2 = new Address("3123 W. Glenwood Street", "Dog House 2", "Outside", "Springfield", "Missouri", "MO", "65807", " ", true);  
  $dbMgr = new DbManager;
  
  $conn = $dbMgr->dbConnect();

  $dbFunctions = new DbFunctions($conn);

  $ids = $dbFunctions->dbInsert($person,$address);
  $ids2 = $dbFunctions->dbInsert($person2,$address2);

  $person->setId($ids[0]);
  $address->setId($ids[1]);

  $person2->setId($ids[0]);
  $address2->setId($ids[1]);
  
  $dbMgr->dbDisconnect();
  
  echo "unitTestInsert Success!<br/>";
}

function unitTestSearch()
{

    $dbMgr = new DbManager;
  
    $conn = $dbMgr->dbConnect();

    $addressFetch = new AddressFetch($conn);

    $address = new Address(null, "", "outside", "", "", "", "", "", null);
//    $rows = $addressFetch->fetchAddress($address);
    $rows = $addressFetch->fetchPersonAddress($address);

    $dbMgr->dbDisconnect();

//    print_r($rows);
    echo "<table style='border: solid 1px black;'>";
	 echo "<tr><th>Select</th><th>First</th><th>Middle</th><th>Last</th><th>Addr1</th><th>Addr2</th><th>Addr3</th><th>City</th><th>State</th><th>StateAbbr</th><th>ZipCode</th><th>ZipCode4</th></tr>";
    foreach(new TableRows(new RecursiveArrayIterator($rows)) as $k=>$v) 
    { 
		if($k != 'ID' && $k != 'Active' && $k != 'PersonID' && $k != 'AddressID') 
		{        
        echo $v;
      }
    }
		
//	 echo "</table>";	
}
//unitTestInsert();

unitTestSearch();