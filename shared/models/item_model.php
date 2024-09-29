<?php

class Item_model extends Model
{
	public $id ;
	public $name;		
	public $category_id;	
	public $type;	
	public $createby;
	public $updateby;
	public $createdt;
	public $updatedt;
	public $deleted;

    function __construct() 
    {
        parent::__construct();
		$this->__pkey = 'id' ;
    }
	function getDetails($id)
	{
		$sql = "SELECT  i.id,i.name,i.category_id,i.type,c.id as categoryid,c.name as categoryname FROM item i
				LEFT JOIN category c ON c.id=i.category_id
				WHERE i.id='$id'" ;

		return $this->db->fetchRow($sql) ;
	}
	function getItems()
	{
	  $sql = "SELECT i.name,i.id, c.name FROM item i
		  INNER JOIN category c ON c.id = i.category_id
				WHERE i.deleted='0'" ;
		return $this->db->fetchRowSet($sql);
	}
	function getItemList()
	{
	  $sql = "SELECT i.name,i.id, c.name FROM item i
				LEFT JOIN category c ON c.id=i.category_id
				WHERE i.deleted='0' ORDER BY c.name ASC, i.name ASC" ;
		return $this->db->fetchRowSet($sql);
	}	
}