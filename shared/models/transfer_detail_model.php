<?php

class Transfer_detail_model extends Model
{
	public $id;
	public $transfer_id;
	public $item_id;
	public $quantity ;

	function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'il_id' ;
    }
	function transferDetails($transferId)
	{
		$sqlt = "SELECT td.*, i.name AS item, c.name AS category, c.id AS category_id, i.id as item_id FROM transfer_detail td
				INNER JOIN item i ON i.id = td.item_id
				LEFT JOIN category c ON c.id = i.category_id WHERE td.transfer_id='$transferId' " ;
		return $this->db->fetchRowSet($sqlt) ;
	}
	function getLocations()
	{
		$sql = "SELECT 'F' AS type, fac_id AS id, fac_name AS name FROM facilities WHERE fac_suspended=0 "
				. " UNION "
				. " SELECT 'S' AS store, is_id AS id, is_name AS name FROM inventory_stores WHERE is_deleted=0 and (is_id != " . QC_STR_DISPOSED . " AND is_id != '" . QC_STR_WRITEOFF . "') " ;
		
		return $this->db->fetchRowSet($sql) ;
	}
	function getQuantity($item,$type,$toid)
	{
		switch($type)
		{
			case QC_INV_FACILITY:
				$sql="SELECT COUNT(*) as quantity FROM inventory_location il
					INNER JOIN facilities f ON f.fac_id=il.il_to_id and il.il_to_type='$type'
					WHERE fac_suspended=0 and fac_deleted=0 and il.il_itm_id='$item' and il.il_to_id='$toid' and il.il_to_type='$type'";
	
				return $this->db->scalarField($sql) ;
				break;
			case QC_INV_STORE:
				$sql="SELECT COUNT(*) as quantity FROM inventory_location il
					INNER JOIN inventory_stores  s ON s.is_id=il.il_to_id and il.il_to_type='$type'
				  WHERE is_deleted=0 and is_hidden=0 and il.il_itm_id='$item' and il.il_to_id='$toid' and il.il_to_type='$type'";
				
		     return $this->db->scalarField($sql) ;
				break;
		}
		
	}
	function getItemLocations($item,$fac)
	{
		$sql = "SELECT COUNT(*) as quantity, il_to_id, il_itm_id, name, type FROM inventory_location il"
				. " INNER JOIN("
				. " SELECT 'F' AS type, fac_id AS id, fac_name AS name FROM facilities WHERE fac_suspended=0 "
				. " UNION "
				. " SELECT 'S' AS type, is_id AS id, is_name AS name FROM inventory_stores WHERE is_deleted=0 and is_hidden=0) AS fs ON il.il_to_id=fs.id AND il.il_to_type=fs.type WHERE il.il_itm_id='$item'  GROUP BY CONCAT(il.il_to_id, '-', il.il_itm_id, '-', il.il_to_type) ORDER BY type" ;

		return $this->db->fetchRowSet($sql) ;
	}
	function getFacilityItemsQty($facid,$itmid)
	{
		$sql = "SELECT COUNT(*) as quantity FROM inventory_location il"
				. " INNER JOIN("
				. " SELECT 'F' AS type, fac_id AS id, fac_name AS name FROM facilities WHERE fac_suspended=0 "
				. " UNION "
				. " SELECT 'S' AS type, is_id AS id, is_name AS name FROM inventory_stores WHERE is_deleted=0) AS fs ON il.il_to_id=fs.id AND il.il_to_type=fs.type "
				. " INNER JOIN items i ON i.itm_id=il_itm_id WHERE fs.type='F' AND il.il_to_id='$facid' AND il.il_itm_id='$itmid' GROUP BY il.il_itm_id" ;

		return $this->db->scalarField($sql) ;
	}
	function getFacilityItems($facId)
	{
		$sql = "SELECT COUNT(*) as quantity, il_itm_id, i.itm_name FROM inventory_location il"
				. " INNER JOIN("
				. " SELECT 'F' AS type, fac_id AS id, fac_name AS name FROM facilities WHERE fac_suspended=0 "
				. " UNION "
				. " SELECT 'S' AS type, is_id AS id, is_name AS name FROM inventory_stores WHERE is_deleted=0) AS fs ON il.il_to_id=fs.id AND il.il_to_type=fs.type "
				. " INNER JOIN items i ON i.itm_id=il_itm_id WHERE fs.type='F' AND il.il_to_id='$facId' GROUP BY il.il_itm_id" ;

		return $this->db->fetchRowSet($sql) ;
	}
	function getStoreItems($stId)
	{
		$sql = "SELECT COUNT(*) as quantity, il_itm_id, i.itm_name FROM inventory_location il"
				. " INNER JOIN("
				. " SELECT 'S' AS type, is_id AS id, is_name AS name FROM inventory_stores WHERE is_deleted=0) AS fs ON il.il_to_id=fs.id AND il.il_to_type=fs.type "
				. " INNER JOIN items i ON i.itm_id=il_itm_id WHERE fs.type='S' AND il.il_to_id='$stId' GROUP BY il.il_itm_id" ;

		return $this->db->fetchRowSet($sql) ;
	}
	function getStoreItemsQty($stId,$itmid)
	{
		$sql = "SELECT COUNT(*) as quantity FROM inventory_location il"
				. " INNER JOIN("
				. " SELECT 'S' AS type, is_id AS id, is_name AS name FROM inventory_stores WHERE is_deleted=0) AS fs ON il.il_to_id=fs.id AND il.il_to_type=fs.type "
				. " INNER JOIN items i ON i.itm_id=il_itm_id WHERE fs.type='S' AND il.il_to_id='$stId' AND il.il_itm_id='$itmid' GROUP BY il.il_itm_id" ;

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
		$limit = intval($limit) ;
		$sql = "SELECT il.il_itm_slno FROM inventory_location il"
				. " INNER JOIN("
				. " SELECT 'F' AS type, fac_id AS id, fac_name AS name FROM facilities WHERE fac_suspended=0 "
				. " UNION "
				. " SELECT 'S' AS type, is_id AS id, is_name AS name FROM inventory_stores WHERE is_deleted=0) AS fs ON il.il_to_id=fs.id AND il.il_to_type=fs.type WHERE il.il_itm_id='$item' AND il.il_to_type='$type' AND il.il_to_id='$from' LIMIT $limit" ;

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
		$checkjunk = $this->getItemDetails($id);
		$frm_id = $checkjunk['il_from_id'];
		$frm_type = $checkjunk['il_from_type'];
		$junk = 0;
		if($frm_id == QC_STR_JUNK && $frm_type == QC_INV_STORE)
		{
			$junk=1;
		}
		$jksz = '' ;
		if($junk==0)
		{
			$jksz = "or (isr.is_id=it.il_to_id and it.il_to_type='S')" ;
		}

		$sql = "SELECT it.il_transfer_type, it.il_to_type, count(it.il_itm_id) as cnt, i.itm_name,isr.is_name, IF( f1.fac_name IS NULL, f2.fac_name, f1.fac_name) as fac_name FROM inventory_transfer it
				LEFT JOIN facilities f1 ON (f1.fac_id=it.il_to_id and it.il_to_type='F' ) 
				LEFT JOIN facilities f2 ON (f2.fac_id=it.il_from_id and it.il_from_type='F')
				LEFT JOIN inventory_stores isr ON (isr.is_id=it.il_from_id and it.il_from_type='S') $jksz
				LEFT JOIN items i ON i.itm_id=it.il_itm_id
				WHERE it.il_transfer_id='$id' GROUP BY it.il_itm_id, it.il_transfer_id " ;

		return $this->db->fetchRowSet($sql);
	}
	function getItemDetails($id)
	{
		$sql="SELECT it.il_from_id,it.il_from_type FROM inventory_transfer it
			  WHERE il_transfer_id='$id' ";
		return $this->db->fetchRow($sql);
	}
}