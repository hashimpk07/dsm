<?php

class Parameters_model extends Model
{
	public $pm_id ;
	public $pm_title;	
	public $pm_code ;	
	public $pm_value ;	
	public $pm_html ;
	public $pm_editable;	
	public $pm_createby;
    public $pm_createdt;
    public $pm_updateby;
    public $pm_updatedt;

    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'pm_id' ;
		
    }
	
	function getDetails($id)
	{
		$sql = "SELECT p.pm_id,p.pm_title,p.pm_code,p.pm_value  FROM parameters p
                 WHERE p.pm_id='$id'" ;

		return $this->db->fetchRow($sql) ;
	}
}