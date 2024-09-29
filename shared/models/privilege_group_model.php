<?php

class Privilege_group_model extends Model
{
	public $id ;		
	public $name ;		
	public $code ;		
	public $user_group_id;		

	/* Branch Model */
    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
	function getDefaultTypes()
	{
		$sql = "SELECT * FROM privilege_group WHERE deleted=0 AND id != '" . QC_USR_SUPERADMIN . "' " ;
		return $this->db->fetchRowSet($sql) ;
	}
}