<?php

class Font_model extends Model
{
	public $font ;
	public $title ;
	
	/* Branch Model */
    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'font' ;
    }
}