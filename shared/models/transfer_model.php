<?php
include_once 'transfer_detail_model.php' ;

class Transfer_model extends Model
{
	public $id;
	public $type ;
	public $dt ;
	public $from_department_id ;
	public $to_department_id ;
	public $createby ;
	public $createdt ;

	function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
	function getLocations()
	{
		$sql = "SELECT 'F' AS type, fac_id AS id, fac_name AS name FROM facilities WHERE fac_suspended=0 "
				. " UNION "
				. " SELECT 'S' AS store, is_id AS id, is_name AS name FROM inventory_stores WHERE is_deleted=0 and (is_id != " . QC_STR_DISPOSED . " AND is_id != '" . QC_STR_WRITEOFF . "')" ;
		
		return $this->db->fetchRowSet($sql) ;
	}
	function getQuantity($item, $deptId)
	{
		$sql="SELECT quantity FROM stock WHERE item_id='$item' AND department_id='$deptId' ";
		return $this->db->scalarField($sql) ;
	}
	function getQuantityEdit($item, $deptId, $editId)
	{
		$sql="SELECT quantity FROM stock WHERE item_id='$item' AND department_id='$deptId' ";
		$stock = $this->db->scalarField($sql) ;
		$sqle = "SELECT quantity FROM transfer_detail WHERE transfer_id='$editId' AND item_id='$item' " ;
		$stock_extra = $this->db->scalarField($sqle) ;
		
		return ($stock + $stock_extra) ;
	}
	function getItemLocations($item)
	{
		$sql = "SELECT ls_stock as quantity, ls_loc_id, ls_itm_id, name, type, i.itm_id, i.itm_name, c.cat_name FROM location_stock ls "
				. " INNER JOIN items i ON i.itm_id=ls.ls_itm_id 
					INNER JOIN categories c ON c.cat_id = i.itm_cat_id
					INNER JOIN (
						SELECT 'F' as type, f.fac_name AS name, f.fac_id AS id FROM facilities f 
						UNION 
						SELECT 'S' as type, i.is_name AS name, i.is_id AS id FROM inventory_stores i WHERE (i.is_id > 3 OR i.is_id=1)
					) AS f ON f.id = ls.ls_loc_id AND f.type = ls.ls_loc_type WHERE ls_itm_id='$item' AND i.itm_deleted=0 " ;

		return $this->db->fetchRowSet($sql) ;
	}
	function getAllItemLocations()
	{
		$sql = "SELECT ls_stock as quantity, ls_loc_id, ls_itm_id, name, type, i.itm_id, i.itm_name, c.cat_name FROM location_stock ls "
				. " INNER JOIN items i ON i.itm_id=ls.ls_itm_id 
					INNER JOIN categories c ON c.cat_id = i.itm_cat_id
					INNER JOIN (
						SELECT 'F' as type, f.fac_name AS name, f.fac_id AS id FROM facilities f 
						UNION 
						SELECT 'S' as type, i.is_name AS name, i.is_id AS id FROM inventory_stores i WHERE (i.is_id > 3 OR i.is_id=1)
					) AS f ON f.id = ls.ls_loc_id AND f.type = ls.ls_loc_type WHERE i.itm_deleted=0 GROUP BY ls.ls_itm_id ORDER BY c.cat_name ASC, i.itm_name ASC" ;

		return $this->db->fetchRowSet($sql) ;
	}
	function getFacilityItemsQty($facid,$itmid)
	{
		$sql = "SELECT ls_stock as quantity FROM location_stock ls WHERE ls.ls_loc_type='F' AND ls_loc_id='$facid' AND ls_itm_id='$itmid' " ;

		return $this->db->scalarField($sql) ;
	}
	function getFacilityItems($facId)
	{
		$sql = "SELECT ls_stock as quantity, ls_itm_id, i.itm_name, i.itm_id, c.cat_name FROM location_stock ls "
				. " INNER JOIN items i ON i.itm_id=ls.ls_itm_id "
			. " INNER JOIN categories c ON c.cat_id = i.itm_cat_id"
			. " WHERE ls_loc_type='F' AND ls_loc_id='$facId' AND ls_stock > 0 " ;

		return $this->db->fetchRowSet($sql) ;
	}
	function getStoreItems($stId)
	{
		$sql = "SELECT ls.ls_stock as quantity, ls_itm_id, i.itm_name, i.itm_id, c.cat_name FROM location_stock ls "
				. " INNER JOIN items i ON i.itm_id=ls.ls_itm_id "
			. " INNER JOIN categories c ON c.cat_id = i.itm_cat_id "
			. " WHERE ls.ls_loc_id='$stId' AND ls.ls_loc_type='S'  AND ls_stock > 0" ;

		return $this->db->fetchRowSet($sql) ;
	}
	function getStoreItemsQty($stId,$itmid)
	{
		$sql = "SELECT ls_stock as quantity FROM location_stock ls WHERE ls.ls_loc_id='$stId' AND ls_itm_id='$itmid' AND ls_loc_type='S'" ;

	    return $this->db->scalarField($sql) ;
	}
	function getAllLocations()
	{
		$sql = "SELECT 'F' AS type, fac_id AS id, fac_name AS name FROM facilities WHERE fac_suspended=0 "
				. " UNION "
				. " SELECT 'S' AS type, is_id AS id, is_name AS name FROM inventory_stores WHERE is_deleted=0 and (is_id != ".QC_STR_DISPOSED . " AND is_id != '" . QC_STR_WRITEOFF . "') " ;
		return $this->db->fetchRowSet($sql) ;
	}
	function getSerialsForTransfer($item, $from, $type, $limit )
	{
		//check transferable
		$sqlConsume = "SELECT itm_consumable FROM items WHERE itm_id='$item' " ;
		$_consumable = $this->db->scalarField($sqlConsume) ;
		if( $_consumable )
		{
			return false ;
		}

		$limit = intval($limit) ;
		$sql = "SELECT its_slno FROM inventory_transfer_serial its "
			. " INNER JOIN inventory_transfer_detail itd ON itd.itd_id=its.its_itd_id WHERE itd_itm_id='$item' AND its_loc_id='$from' AND its_loc_type='$type' LIMIT $limit " ;

		return $this->db->fetchRowSet($sql) ;
	}
	function getLocationName($id, $type)
	{
		if( $type == 'F' )
		{
			$sql = "SELECT fac_name AS name FROM facilities WHERE fac_id='$id' AND fac_suspended=0" ;
			return $this->db->scalarField($sql) ;
		}
		else if( $type == 'S' )
		{
			$sql = "SELECT is_name AS name FROM inventory_stores WHERE is_id='$id' AND is_deleted=0" ;
			return $this->db->scalarField($sql) ;
		}
		return false ;
	}
	function getInventoryItems($id)
	{
		$sql = "SELECT td.*, i.name as item, c.name AS category FROM transfer t
				INNER JOIN transfer_detail td ON td.transfer_id = t.id
				LEFT JOIN item i ON i.id = td.item_id 
				LEFT JOIN category c ON c.id = i.category_id WHERE t.id= '$id'" ;
		return $this->db->fetchRowSet($sql);
	}
	function getItemDetails($id)
	{
		$sql="SELECT it.from_id, it.from_type FROM inventory_transfer it
			  WHERE id='$id' ";
		return $this->db->fetchRow($sql);
	}
	function getInfo($id)
	{
		$sql = "SELECT t.*, f.name as from_department, t1.name as to_department, e.name as employee, 
					lf.name as from_location, lt.name as to_location, lf.id AS from_location_id, lt.id AS to_location_id FROM transfer t 
				LEFT JOIN department f ON f.id = t.from_department_id
				LEFT JOIN department t1 ON t1.id = t.to_department_id
				LEFT JOIN location lf ON lf.id = f.location_id
				LEFT JOIN location lt ON lt.id = t1.location_id
				LEFT JOIN employee e ON e.id = t.createby
				WHERE t.id = $id" ;
		return $this->db->fetchRow($sql) ;
	}
}