<?php
namespace CL_Test\V1;
abstract class TblRow //The abstract parent for Person and Abstract
{
    protected $id;
    protected $active;
    
   public function setId($id)
	{
		$this->id = $id;	
	}
	
	public function setActive($active)
	{
		$this->active = $active;	
	}		
	
	public function getId()
	{
		return $this->id;	
	}
	
	public function isActive()
	{
		return $this->active;	
	}	
}
