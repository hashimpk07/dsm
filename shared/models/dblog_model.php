<?php

class Dblog_model extends Model
{
	public $log_id ;
	public $log_emp_id;	
	public $log_action ;	
	public $log_data ;	
	public $log_dt ;
        public $emp_name;
	public $log_view;	
	

    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'log_id' ;
		
    }
	
	function getDetails($id)
	{
		$sql = "SELECT m.log_id,m.log_emp_id,m.log_action,m.log_data,m.log_dt,e.emp_name FROM db_log as m  inner join employee  e  on  m.log_emp_id=e.emp_id and
                         m.log_id='$id'" ;
		return $this->db->fetchRow($sql) ;
	}
}