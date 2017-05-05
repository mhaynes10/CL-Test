<?php
namespace CL_Test\V1;
include_once 'tblrow.php';
class Address extends TblRow
{
   private $addr1;
   private $addr2;
   private $addr3;
   private $city;
   private $state;
   private $stateAbbr;
   private $zipCode;
   private $zipCode4;
			
	public function __construct($addr1, $addr2, $addr3, $city, $state, $stateAbbr, $zipCode, $zipCode4, $active)
	{
		$this->setAddr1($addr1);
		$this->setAddr2($addr2);
		$this->setAddr3($addr3);
		$this->setCity($city);
		$this->setState($state);
		$this->setStateAbbr($stateAbbr);
		$this->setZipCode($zipCode);
		$this->setZipCode4($zipCode4);
		$this->setActive($active);
	}

	public function setAddr1($addr1)
	{
		$this->addr1 = $addr1;	
	}
	public function setAddr2($addr2)
	{
		$this->addr2 = $addr2;	
	}
	public function setAddr3($addr3)
	{
		$this->addr3 = $addr3;	
	}
	public function setCity($city)
	{
		$this->city = $city;	
	}
	public function setState($state)
	{
		$this->state = $state;	
	}
	public function setStateAbbr($stateAbbr)
	{
		$this->stateAbbr = $stateAbbr;	
	}
	public function setZipCode($zipCode)
	{
		$this->zipCode = $zipCode;	
	}
	public function setZipCode4($zipCode4)
	{
		$this->zipCode4 = $zipCode4;	
	}
	
	public function getAddr1()
	{
		return $this->addr1;	
	}
	public function getAddr2()
	{
		return $this->addr2;	
	}
	public function getAddr3()
	{
		return $this->addr3;	
	}
	public function getCity()
	{
		return $this->city;	
	}
	public function getState()
	{
		return $this->state;	
	}
	public function getStateAbbr()
	{
		return $this->stateAbbr;	
	}
	public function getZipCode()
	{
		return $this->zipCode;	
	}
	public function getZipCode4()
	{
		return $this->zipCode4;	
	}
}
