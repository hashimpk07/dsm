<?php
include_once 'inventory_transfer_detail_model.php' ;

class Inventory_transfer_model extends Model
{
	public $it_id;
	public $it_type ;
	public $it_dt ;
	public $it_createby ;
	public $it_createdt ;

	function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'it_id' ;
    }
	function getLocations()
	{
		$sql = "SELECT 'F' AS type, fac_id AS id, fac_name AS name FROM facilities WHERE fac_suspended=0 "
				. " UNION "
				. " SELECT 'S' AS store, is_id AS id, is_name AS name FROM inventory_stores WHERE is_deleted=0 and (is_id != ".QC_STR_DISPOSED . " AND is_id != '" . QC_STR_WRITEOFF . "')" ;
		
		return $this->db->fetchRowSet($sql) ;
	}
	function getQuantity($item,$type,$toid)
	{
		switch($type)
		{
			case QC_INV_FACILITY:
				$sql="SELECT ls_stock as quantity FROM location_stock ls WHERE ls_loc_type ='F' AND ls.ls_itm_id='$item' and ls.ls_loc_id='$toid' ";
	
				return $this->db->scalarField($sql) ;
				break;
			case QC_INV_STORE:
				$sql="SELECT ls_stock as quantity FROM location_stock ls WHERE ls_loc_type ='S' AND ls.ls_itm_id='$item' and ls.ls_loc_id='$toid' ";
				
		     return $this->db->scalarField($sql) ;
				break;
		}
	}
	function getItemLocations($item)
	{
		$sql = "SELECT ls_stock as quantity, ls_loc_id, ls_itm_id, name, type, i.itm_id, i.itm_name, c.cat_name FROM location_stock ls "
				. " INNER JOIN items i ON i.itm_id=ls.ls_itm_id 
					INNER JOIN category c ON c.cat_id = i.itm_cat_id
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
					INNER JOIN category c ON c.cat_id = i.itm_cat_id
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
			. " INNER JOIN category c ON c.cat_id = i.itm_cat_id"
			. " WHERE ls_loc_type='F' AND ls_loc_id='$facId' AND ls_stock > 0 " ;

		return $this->db->fetchRowSet($sql) ;
	}
	function getStoreItems($stId)
	{
		$sql = "SELECT ls.ls_stock as quantity, ls_itm_id, i.itm_name, i.itm_id, c.cat_name FROM location_stock ls "
				. " INNER JOIN items i ON i.itm_id=ls.ls_itm_id "
			. " INNER JOIN category c ON c.cat_id = i.itm_cat_id "
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
//		$checkjunk = $this->getItemDetails($id);
//		$frm_id = $checkjunk['il_from_id'];
//		$frm_type = $checkjunk['il_from_type'];
//		$junk = 0;
//		if($frm_id == QC_STR_JUNK && $frm_type == QC_INV_STORE)
//		{
//			$junk=1;
//		}
//		$jksz = '' ;
//		if($junk==0)
//		{
//			$jksz = "or (isr.is_id=it.il_to_id and it.il_to_type='S')" ;
//		}

		$sql = "SELECT itd.*, i.itm_name, f.name AS from_name, t.id as to_id, t.type as to_type, t.name as to_name FROM inventory_transfer_detail itd "
			. " INNER JOIN items i ON i.itm_id = itd.itd_itm_id "
			. " LEFT JOIN (
				SELECT 'F' as type, f.fac_name AS name, f.fac_id AS id FROM facilities f 
				UNION 
				SELECT 'S' as type, i.is_name AS name, i.is_id AS id FROM inventory_stores i
				) AS f ON f.id = itd.itd_from_id AND f.type = itd.itd_from_type

				LEFT JOIN (
				SELECT 'F' as type, f.fac_name AS name, f.fac_id AS id FROM facilities f 
				UNION 
				SELECT 'S' as type, i.is_name AS name, i.is_id AS id FROM inventory_stores i
				) AS t ON t.id = itd.itd_to_id AND t.type = itd.itd_to_type
				
				 WHERE itd.itd_it_id='$id' " ;

		return $this->db->fetchRowSet($sql);
	}
	function getItemDetails($id)
	{
		$sql="SELECT it.it_from_id, it.it_from_type FROM inventory_transfer it
			  WHERE it_id='$id' ";
		return $this->db->fetchRow($sql);
	}
}