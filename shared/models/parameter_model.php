<?php
class Parameter_model extends Model
{
	public $id ;
	public $code;	
	public $value ;
	public $createby;
    public $createdt;
    public $updateby;
    public $updatedt;

    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
		
    }
	
	function getDetails($id)
	{
		$sql = "SELECT p.id,p.code,p.value  FROM parameter p
                 WHERE p.id='$id'" ;

		return $this->db->fetchRow($sql) ;
	}
	function getDetailscode()
	{
		$sql = "SELECT p.id,p.code,p.value  FROM parameter p
                 WHERE p.code='PRM_THEME_BG'" ;
		 return $this->db->fetchRow($sql) ;
	}
}