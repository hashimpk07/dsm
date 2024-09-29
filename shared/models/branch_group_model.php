<?php

class Branch_group_model extends Model
{
	public $id;
	public $name;
	
	/* Branch Model */
    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
	
}