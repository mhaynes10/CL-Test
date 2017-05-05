<?php
namespace CL_Test\V1;
include_once 'tblrow.php';
class Person extends TblRow
{
	private $first;
	private $middle;
	private $last;

	public function __construct($first, $middle, $last, $active)
	{
		$this->setFirst($first);
		$this->setMiddle($middle);
		$this->setLast($last);
		$this->setActive($active);
	}
	
	public function setFirst($first)
	{
		$this->first = $first;	
	}
	public function setMiddle($middle)
	{
		$this->middle = $middle;	
	}
	public function setLast($last)
	{
		$this->last = $last;	
	}
	
	public function getFirst()
	{
		return $this->first;	
	}
	public function getMiddle()
	{
		return $this->middle;	
	}
	public function getLast()
	{
		return $this->last;	
	}
}
