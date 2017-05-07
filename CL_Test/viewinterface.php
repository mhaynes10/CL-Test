<?php
namespace CL_Test\V1;

//File viewinterface.php is the interface to index.html
//It receives the action requests from index.html and sends back content
//No functions or classes are defined here 

include_once 'dbmanager.php';
include_once 'person.php';
include_once 'address.php';
include_once 'dbfunctions.php';
include_once 'search.php';

//First get the requested action
$action = $_GET["action"];

//Instantiate new DbManager $dbMgr and new Search $search
//The connection $conn is fed to the $search object for accessing the database
 
$dbMgr = new DbManager;
$conn = $dbMgr->dbConnect();

$search = new Search($conn);

//This "if-else if" block handles the actions requested from index.html JavaSript.
//It gets the arguments instantiates/creates various objects/properties, including Address and Person, as needed 
//and instantiates a new DbFunctions $dbFunctions as necessary to handle add, update and delete requests.
//$search is used to gather query results.

//Note: Addr1, Addr2, Addr3, represent lines in a single address, not multiple addresses

// "add" a new Person-Address pair. If either already exists, it will be given a new relation as requested but not duplicated.
if($action == "add") 
{
  $message = "Record Added<br/>";  
  $dbFunctions = new DbFunctions($conn);
  $address = new Address($_GET["addr1"], $_GET["addr2"], $_GET["addr3"], $_GET["city"], $_GET["state"], $_GET["stateabbr"], $_GET["zipcode"], $_GET["zipcode4"], true);
  $person = new Person($_GET["first"], $_GET["middle"], $_GET["last"], true);
  $ids = $dbFunctions->dbInsert($person,$address,$search);	
  $rows = $search->fetchPersonAddressByRelation($ids[2]);
}
// "update" a new Person-Address pair. 
//If updated Person data is already present in the database, an error message is sent back
else if($action == "update") 
{
  $message = "Record Updated<br/>";  
  $dbFunctions = new DbFunctions($conn);
  $address = new Address($_GET["addr1"], $_GET["addr2"], $_GET["addr3"], $_GET["city"], $_GET["state"], $_GET["stateabbr"], $_GET["zipcode"], $_GET["zipcode4"], true);
  $person = new Person($_GET["first"], $_GET["middle"], $_GET["last"], true);
  $ids = $dbFunctions->dbUpdate($_GET["relId"], $person,$address,$search,$_GET["single"]);	
  $rows = $search->fetchPersonAddressByRelation($ids[2]);
  if($ids[0] == 0) 
  {
	  $message = "Record <em>not</em> updated! Person already exists<br/>";
  }
}
// "search" for a list of Persons attached to Addresses matching the search criteria
else if($action == "search") 
{
  $message = "Results found<br/>";  
  $address = new Address($_GET["addr1"], $_GET["addr2"], $_GET["addr3"], $_GET["city"], $_GET["state"], $_GET["stateabbr"], $_GET["zipcode"], $_GET["zipcode4"], true);
  $rows = $search->fetchPersonAddress($address);
}
//"updateRequest" is sent prior to an update. It asks Search for a count of other Persons that
// may be impacted by an address change. This information allows the user a choice in how to proceed before updating
elseif($action == "updateRequest") 
{
  $relId = $_GET["relId"];
  $rows = $search->fetchPersonAddressByRelation($relId);
  $addressID = $rows[0]['AddressID'];  
  $personCountArray = $search->fetchPersonCount($addressID);
  $personCount = $personCountArray[0]['count(*)'];
}
//"delete" removes the relation between the chosen Person-Address pair. if the Person and/or Address have no other attachments
//they will be removed as well.
elseif($action == "delete") 
{
  $message = "Record Deleted<br/>";  
  $dbFunctions = new DbFunctions($conn);
  $relId = $_GET["relId"];
  $addrAry = $dbFunctions->dbDelete($relId, $search);	
  $address = new Address($addrAry["Addr1"], $addrAry["Addr2"], $addrAry["Addr3"], $addrAry["City"], $addrAry["State"], $addrAry["StateAbbr"], $addrAry["ZipCode"], $addrAry["ZipCode4"], $addrAry["Active"]);	
  $rows = $search->fetchPersonAddress($address);
}

//At this point the db connection is not needed, so disconnect.

$dbMgr->dbDisconnect();

//In this section: 
//Depending on the requested action and other properties, results from $search get parsed into an html table.
//That and any other supporting content and controls are echoed back to the calling index.html. 

if($action == "add" || $action == "search" || $action == "update" || $action == "delete")
{ 
  if(count($rows) == 0) 
  {
  	  echo "<span style='font-size:calc(12px + 1vw);'>No results found for search.<br/><a href='index.html'>New Search</a></span>";	
  }
  else 
  {
     echo "<span style='font-size:calc(12px + 1vw);'>".$message."</span>";	
     if($action == "search" || $action == "add") 
     {
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
     }
     echo "<br/>";	
     echo "<a href='index.html'>New Search</a>";
  }
}
//Note: Since the user "sees" multiple occurrences of an address, verbiage in $prompt is written with that in mind,
//though in reality only one record per address exists, and only one record per person exists.
// Here, the Person-Address pair to be updated are written into a form for editing instead of a table
elseif($action == "updateRequest")
{
  $rowID = $rows[0]['RelID'];
  $button = "<button type='button' onclick='submitChange(".$rowID.")'>Submit</button><button type='button' onclick='cancelAction()'>Cancel</button>";

  if($personCount > 1)
  {
  	 $prompt = "<p/>More than one person attached to this address. If updating address, please select:<br/> <input type='radio' id='single' name='updateType' value='only_one' checked>Update for only this person.<br/>
  <input type='radio' id='multiple' name='updateType' value='all'>Update for all<br/>"; 
  } 
  else 
  {
  	 $prompt = "<p/>";
  }
  
  echo "<div style='width:290px;float:left;'>";
  echo "<form style=' margin-top: 20px;'>";

  echo "<h4>Person</h4>";
  echo "<div style='text-align:right;width:100px;float:left;'>";
  echo "<label class='inputLabel' for='FirstX'>First Name:</label><br/>";
  echo "<label class='inputLabel' for='MiddleX'>Middle Name:</label><br/>";
  echo "<label class='inputLabel' for='LastX'>Last Name:</label><br/>";
  echo "</div>";
  echo "<div style='text-align:left;width:180px;float:right;'>";
  echo "<input class='textBox' type='text' id='FirstX' value ='".$rows[0]['First']."'/><br/>";
  echo "<input class='textBox' type='text' id='MiddleX' value ='".$rows[0]['Middle']."'/><br/>";
  echo "<input class='textBox' type='text' id='LastX' value ='".$rows[0]['Last']."'/><br/>";
  echo "</div>";

  echo "<div style='width:280px;float:left;'>";
  echo "<hr/>";
  echo "</div>";

  echo "<h4>Address</h4>";
  echo "<div style='text-align:right;width:100px;float:left;'>";
  echo "<label class='inputLabel' for='Addr1X'>Address Line 1:</label><br/>";
  echo "<label class='inputLabel' for='Addr2X'>Address Line 2:</label><br/>";
  echo "<label class='inputLabel' for='Addr3X'>Address Line 3:</label><br/>";
  echo "<label class='inputLabel' for='CityX'>City:</label><br/>";
  echo "<label class='inputLabel' for='StateX'>State:</label><br/>";
  echo "<label class='inputLabel' for='StateAbbrX'>State Abbr:</label><br/>";
  echo "<label class='inputLabel' for='ZipCodeX'>Zip Code:</label><br/>";
  echo "<label class='inputLabel' for='ZipCode4X'>4 digit zip ext:</label><br/>";
  echo "</div>";
  echo "<div style='text-align:left;width:180px;float:right;'>";
  echo "<input class='textBox' type='text' id='Addr1X' value ='".$rows[0]['Addr1']."' /><br/>";
  echo "<input class='textBox' type='text' id='Addr2X' value ='".$rows[0]['Addr2']."' /><br/>";
  echo "<input class='textBox' type='text' id='Addr3X' value ='".$rows[0]['Addr3']."' /><br/>";
  echo "<input class='textBox' type='text' id='CityX' value ='".$rows[0]['City']."' /><br/>";
  echo "<input class='textBox' type='text' id='StateX' value ='".$rows[0]['State']."' /><br/>";
  echo "<input class='textBox' type='text' id='StateAbbrX' value ='".$rows[0]['StateAbbr']."' /><br/>";
  echo "<input class='textBox' type='text' id='ZipCodeX' value ='".$rows[0]['ZipCode']."' /><br/>";
  echo "<input class='textBox' type='text' id='ZipCode4X' value ='".$rows[0]['ZipCode4']."' /><br/>";
  echo "</div>";

  echo "<div style='width:280px;float:left;'>";
  echo "<hr/>";
  echo "</div>";
  echo "<div style='float:left;'>";
  echo $prompt.$button."<br/>";
  echo "</form>";	  
  echo "<a href='index.html'>New Search</a>"; 
  echo "</div>";
  echo "</div>";
}

?>

