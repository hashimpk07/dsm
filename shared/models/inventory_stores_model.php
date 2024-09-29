<?php

class Inventory_stores_model extends Model
{
	public $is_id ;
	public $is_name;
	public $is_createby;
    public $is_createdt;
    public $is_updateby;
    public $is_updatedt;
	public $is_deleted;
	public $is_hidden;
		    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'is_id' ;
		
    }
	function getStoresStock()
	{
		$sql = "SELECT is_id,is_name FROM inventory_stores WHERE is_deleted='0'" ;
		return $this->db->fetchKV($sql, 'is_id', 'is_name') ;
	}
	function getStores()
	{
	  $sql = "SELECT ist.is_name,ist.is_id FROM inventory_stores ist
				WHERE ist.is_deleted='0' and ist.is_hidden='0'" ;
		return $this->db->fetchRowSet($sql);
	}
	function getDetails($id)
	{
		$sql = "SELECT ist.is_id,ist.is_name  FROM inventory_stores ist
                 WHERE ist.is_id='$id'" ;

		return $this->db->fetchRow($sql) ;
	}
	function getInventoryItems($id)
	{
		$sql = "SELECT ls.*, i.itm_name FROM location_stock ls "
			. " INNER JOIN items i ON i.itm_id = ls.ls_itm_id WHERE ls.ls_loc_type='S' AND ls.ls_loc_id='$id'" ;
		return $this->db->fetchRowSet($sql);
	}
}