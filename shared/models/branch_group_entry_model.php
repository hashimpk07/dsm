<?php

class Branch_group_entry_model extends Model
{
	public $branch_group_id;
	public $branch_id;
	
	/* Branch Model */
    function __construct() 
    {
        parent::__construct();
		
    }
	function getEntries( $groupId )
	{
		$sql = "SELECT b.name FROM branch_group_entry be
				INNER JOIN branch b ON b.id=be.branch_id WHERE be.branch_group_id='$groupId' " ;
		
		return $this->db->fetchRowSet($sql) ;
	}
	
}