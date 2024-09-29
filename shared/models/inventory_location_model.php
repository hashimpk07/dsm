<?php

class inventory_location_model extends Model
{
	public $il_itm_id ;
	public $il_id;	
	public $il_transfer_id;	
	public $il_itm_slno;	
	public $il_from_id;
	public $il_from_type;	
	public $il_to_id;
    public $il_to_type;
    public $il_dt;

    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'il_id' ;
		
    }
	
	function getDetails($id)
	{
		$sql = "SELECT i.itm_name,i.itm_id,i.itm_type FROM items i
				LEFT JOIN category c ON c.cat_id=i.itm_cat_id
				LEFT JOIN inventory_location il ON il.il_itm_id=i.itm_id
		          WHERE l.lng_id='$id'" ;

		return $this->db->fetchRow($sql) ;
	}
}