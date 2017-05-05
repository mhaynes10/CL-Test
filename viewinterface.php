<?php
namespace CL_Test\V1;
use \RecursiveArrayIterator;
use \RecursiveIteratorIterator;

include_once 'dbmanager.php';
include_once 'person.php';
include_once 'address.php';
include_once 'dbfunctions.php';
include_once 'search.php';


$action = $_GET["action"];

$dbMgr = new DbManager;
$conn = $dbMgr->dbConnect();
$search = new Search($conn);

if($action == "add") 
{
  $message = "Record Added<br/>";  
  $dbFunctions = new DbFunctions($conn);
  $address = new Address($_GET["addr1"], $_GET["addr2"], $_GET["addr3"], $_GET["city"], $_GET["state"], $_GET["stateabbr"], $_GET["zipcode"], $_GET["zipcode4"], true);
  $person = new Person($_GET["first"], $_GET["middle"], $_GET["last"], true);
  $ids = $dbFunctions->dbInsert($person,$address,$search);	
 //  echo json_encode($ids)."<p/>";
  $rows = $search->fetchPersonAddressByRelation($ids[2]);
}
else if($action == "update") 
{
  $message = "Record Updated<br/>";  
  echo "update says single = ".$_GET["single"];
  $dbFunctions = new DbFunctions($conn);
  $address = new Address($_GET["addr1"], $_GET["addr2"], $_GET["addr3"], $_GET["city"], $_GET["state"], $_GET["stateabbr"], $_GET["zipcode"], $_GET["zipcode4"], true);
  $person = new Person($_GET["first"], $_GET["middle"], $_GET["last"], true);
  $ids = $dbFunctions->dbUpdate($_GET["relId"], $person,$address,$search,$_GET["single"]);	
  $rows = $search->fetchPersonAddressByRelation($ids[2]);
  if($ids[0] == 0) 
  {
	  $message = "Record <em>not</em> updated! Person already exists<br/>";
  }
  if($ids[1] == 0) 
  {
	  $message = "Record <em>not</em> updated! Address already exists<br/>";
  }

}
else if($action == "search") 
{
  $message = "Results found<br/>";  
  $address = new Address($_GET["addr1"], $_GET["addr2"], $_GET["addr3"], $_GET["city"], $_GET["state"], $_GET["stateabbr"], $_GET["zipcode"], $_GET["zipcode4"], true);
  $rows = $search->fetchPersonAddress($address);
}
elseif($action == "searchByRel") 
{
  $relId = $_GET["relId"];
  $rows = $search->fetchPersonAddressByRelation($relId);
  //TODO: If necessary, send back indicator that address change could impact other persons
  $addressID = $rows[0]['AddressID'];  
  $personCountArray = $search->fetchPersonCount($addressID);
  $personCount = $personCountArray[0]['count(*)'];
}
elseif($action == "delete") 
{
  $message = "Record Deleted<br/>";  
  $dbFunctions = new DbFunctions($conn);
  $relId = $_GET["relId"];
  $addrAry = $dbFunctions->dbDelete($relId, $search);	
  $address = new Address($addrAry["Addr1"], $addrAry["Addr2"], $addrAry["Addr3"], $addrAry["City"], $addrAry["State"], $addrAry["StateAbbr"], $addrAry["ZipCode"], $addrAry["ZipCode4"], $addrAry["Active"]);	
 
 // echo json_encode($address)."<p/>";
  
  $rows = $search->fetchPersonAddress($address);
}

$dbMgr->dbDisconnect();

//echo json_encode($rows);

if($action == "add" || $action == "search" || $action == "update" || $action == "delete")
{ 
  echo $message;	
  echo "<table style='border: solid 1px black;'>";
  echo "<tr><th>Select</th><th>Last</th><th>First</th><th>Middle</th><th>Addr1</th><th>Addr2</th><th>Addr3</th><th>City</th><th>State</th><th>StateAbbr</th><th>ZipCode</th><th>ZipCode4</th></tr>";
  $td = "<td style='width:800px;border:1px solid black;'>";

  for($i=0;$i<count($rows);$i++) 
  {
	  echo "<tr>";
	  $rowID = $rows[$i]['RelID'];
	  $button = "<button type='button' onclick='editRecord(".$rowID.")'>Edit</button><button type='button' onclick='deleteRecord(".$rowID.")'>Delete</button>";
	  echo $td.$button."<span id='Rel".$rows[$i]['RelID']."' style='visibility:hidden'>".$rows[$i]['RelID']."</span></td>".$td.$rows[$i]['Last']."</td>".$td.$rows[$i]['First']."</td>".$td.$rows[$i]['Middle']."</td>".$td.$rows[$i]['Addr1']."</td>".
	  $td.$rows[$i]['Addr2']."</td>".$td.$rows[$i]['Addr3']."</td>".$td.$rows[$i]['City']."</td>".$td.$rows[$i]['State']."</td>".$td.$rows[$i]['StateAbbr']."</td>".
	  $td.$rows[$i]['ZipCode']."</td>".$td.$rows[$i]['ZipCode4']."</td></tr>";	
  }
		
  echo "</table>";
  echo "<br/>";	
  echo "<a href='index.html'>New Search</a>";
}
elseif($action == "searchByRel")
{
  $rowID = $rows[0]['RelID'];
  $button = "<button type='button' onclick='submitChange(".$rowID.")'>Submit</button><button type='button' onclick='cancelAction()'>Cancel</button>";

  if($personCount > 1)
  {
  	 $prompt = "<p/>More than one person attached to this address.<br/> <input type='radio' id='single' name='updateType' value='only_one' checked>Update for only this person.<br/>
  <input type='radio' id='multiple' name='updateType' value='all'>Update for all<br/>"; 
  } 
  else 
  {
  	 $prompt = "<p/>";
  }
  
  echo "<form>";
  echo "First Name: <input type='text' id='FirstX' value ='".$rows[0]['First']."' /><br/>";
  echo "Middle Name/Initial: <input type='text' id='MiddleX' value ='".$rows[0]['Middle']."' /><br/>";
  echo "Last Name: <input type='text' id='LastX' value ='".$rows[0]['Last']."' /><br/>";
  echo "Address Line 1: <input type='text' id='Addr1X' value ='".$rows[0]['Addr1']."' /><br/>";
  echo "Address Line 2: <input type='text' id='Addr2X' value ='".$rows[0]['Addr2']."' /><br/>";
  echo "Address Line 3: <input type='text' id='Addr3X' value ='".$rows[0]['Addr3']."' /><br/>";
  echo "City: <input type='text' id='CityX' value ='".$rows[0]['City']."' /><br/>";
  echo "State: <input type='text' id='StateX' value ='".$rows[0]['State']."' /><br/>";
  echo "StateAbbr: <input type='text' id='StateAbbrX' value ='".$rows[0]['StateAbbr']."' /><br/>";
  echo "Zip code: <input type='text' id='ZipCodeX' value ='".$rows[0]['ZipCode']."' /> 4 digit zip ext: <input type='text' id='ZipCode4X' value ='".$rows[0]['ZipCode4']."' /><br/>";
  echo $prompt.$button."<br/>";
  echo "</form>";	  
  echo "<a href='index.html'>Home</a>"; 

}

?>

