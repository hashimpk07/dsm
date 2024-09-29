<?php

class City_model extends Model
{
	public $id;
	public $name;		
	public $state_id;
	
	
	/* Branch Model */
    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
}