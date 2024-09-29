<?php

class Language_model extends Model
{
	public $id ;
	public $name ;
	public $font ;
	public $dir ;
	
	
	/* Branch Model */
    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
}