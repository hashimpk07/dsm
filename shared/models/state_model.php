<?php

class State_model extends Model
{
	public $id;
	public $name;		
	public $country_id;
	
	/* Branch Model */
    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
}