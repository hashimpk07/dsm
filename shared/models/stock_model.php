<?php

class stock_model extends Model
{
	public $department_id ;
	public $item_id ;
	public $stock ;

    function __construct() 
    {
        parent::__construct();
    }
	function updateLocationStock( $locIdTo, $itemId, $count, $locIdFrom = null )
	{
		if( $locIdFrom )
		{
			$sqlt = "SELECT type FROM department WHERE id='$locIdFrom'" ;
			$type = $this->db->scalarField($sqlt) ;
			if( $type == QC_DEPT_TYPE_NEW )
			{
				$locIdFrom = 0 ;
			}
		}
		if( $locIdTo )
		{
			$sqlt = "SELECT type FROM department WHERE id='$locIdTo'" ;
			$type = $this->db->scalarField($sqlt) ;
			if( $type == QC_DEPT_TYPE_NEW )
			{
				$locIdTo = 0 ;
			}
		}
		
		// check deductable ok ? {
		if( $locIdFrom )
		{

			$sqlf = "SELECT quantity FROM stock WHERE department_id='$locIdFrom' AND item_id = '$itemId' " ;
			$countOwn = $this->db->scalarField($sqlf) ;
			
			if( $countOwn < $count )
			{
				return false ;
			}
			//deduct stock
			$sqlfu = "UPDATE stock SET quantity = quantity - $count WHERE department_id = '$locIdFrom' AND item_id = '$itemId' LIMIT 1 " ;
			$ret = $this->db->execute($sqlfu) ;
		}
		// }
		if( $locIdTo )
		{
			$sql = "SELECT COUNT(*) as cnt FROM stock WHERE department_id='$locIdTo' AND item_id = '$itemId' " ;
			$ret = false ;
			if( $this->db->scalarField($sql) )
			{
				$sqlu = "UPDATE stock SET quantity = quantity + $count WHERE department_id='$locIdTo' AND item_id = '$itemId' LIMIT 1 " ;
				$ret = $this->db->execute($sqlu) ;
			}
			else
			{
				$sqli = "INSERT INTO stock (department_id, quantity, item_id) VALUES('$locIdTo', '$count', '$itemId') " ;
				$ret = $this->db->execute($sqli) ;
			}
		}
	}
}