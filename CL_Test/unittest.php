<?php
namespace CL_Test\V1;
include 'dbmanager.php';
include 'person.php';
include 'address.php';

function unitTestInsert() 
{
  $person = new Person("Maggie"," ", "Haynes", true);
  $address = new Address("3123 W. Glenwood Street", "Dog House", "Outside", "Springfield", "Missouri", "MO", "65807", " ", true);
  $dbMgr = new DbManager;

  $dbMgr->dbConnect();
 
  $ids = $dbMgr->dbInsert($person,$address);
  $person->setId($ids[0]);
  $address->setId($ids[1]);
  
  $person2 = new Person("Elvira"," ", "Haynes", true);

  $person2->setId($dbMgr->insertPerson($person2));
  $dbMgr->insertRelation($person2->getId(), $address->getId());
  
  $dbMgr->dbDisconnect();
  
  echo "unitTestInsert Success!";
}

unitTestInsert();

