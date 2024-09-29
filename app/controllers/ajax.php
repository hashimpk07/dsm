<?php
class Ajax extends Controller
{
	//Constructor
	public function __construct()
	{
		$acls = array(
			'allow' => array(
						'*' => '*' ),
			'deny' => array(),
			'order' => 'AD', //Allow, Then Deny (Options are "DA" or "AD")
			'user' => false, //Login permisson not required.
		);
		$this->acl($acls) ;

		parent::__construct() ;
	}
	function branch_checks($field_name)
	{
		$editId = $this->getArg('editId') ;

		$country = $this->fields['selCountry'] ;
		$state	 = $this->fields['selState'] ;
		$city	 = $this->fields['selCity'] ;

		$country_str = ' ' ;
		$state_str = ' ' ;
		$city_str = ' ' ;
		if( $country )
		{
			$country_str = " AND c.id='$country' " ;
		}
		if( $state )
		{
			$state_str = " AND s.id='$state' " ;
		}
		if( $city )
		{
			$city_str = " AND ct.id='$city' " ;
		}
		
		$sql = "SELECT b.id, b.name FROM branch b
			JOIN city ct ON ct.id = b.city_id
			JOIN state s ON s.id = ct.state_id
			JOIN country c ON c.id = s.country_id WHERE 1 $country_str $state_str $city_str " ;
		$records = $this->db->fetchRowSet($sql) ;
		//get branch ids
		$group_ids = array() ;
		if( $editId )
		{
			$sqlb = "SELECT branch_id FROM branch_group_entry WHERE branch_group_id='$editId'";
			$group_set = $this->db->fetchRowSet($sqlb) ;
			foreach( $group_set as $one )
			{
				$group_ids[] = $one['branch_id'] ;
			}
		}
		$this->loadView('branch_group_branch_row', array( 'mode' => 'add', 'branches' => $records, 'field_name'=>$field_name, 'sel_branches' => $group_ids )) ;
	}
	function suggest_contents()
	{
		$typein = $_REQUEST['term'] ;
		$sql = "SELECT title, id FROM content WHERE title LIKE '%$typein%' AND deleted=0 AND approved=1 " ;
		$records = $this->db->fetchRowSet($sql, 'assoc') ;
		
		$json = array() ;
		foreach( $records as $data )
		{
			$json[] = array(
				'value' => $data['title'],
				'id' => $data['id'],
			);
		}
		
		jsonResponse($json) ;
	}
	public function screen_window_options($screenId, $value = null, $selectany = true)
	{
		$records = $this->loadModel('window_model')->getWhereBy(array('screen_id' => $screenId)) ;
		if( $selectany )
		{
			echo "<option value='0'>--Select--</option>" ;
		}
		foreach( $records as $v )
		{
			$sel = '' ;
			if( $value !== null )
			{
				if( $value == $v['id'] )
				{
					$sel = 'selected="selected"' ;
				}
			}
			echo "<option $sel value='" . $v['id'] . "'>" . $v['name'] . '(' . $v['id'] . ')' . "</option>" ;
		}
	}
	public function branch_screen_options($branchId, $value = null, $selectany = true)
	{
		$records = $this->loadModel('screen_model')->getWhereBy(array('branch_id' => $branchId, 'deleted' => '0')) ;
		if( $selectany )
		{
			echo "<option value='0'>--Select--</option>" ;
		}
		foreach( $records as $v )
		{
			$sel = '' ;
			if( $value !== null )
			{
				if( $value == $v['id'] )
				{
					$sel = 'selected="selected"' ;
				}
			}
			echo "<option $sel value='" . $v['id'] . "'>" . $v['name'] . '(' . $v['id'] . ')' . "</option>" ;
		}
	}
	public function branchgroup_screen_options($groupId, $value = null, $selectany = true)
	{
		$sql = "SELECT branch_id FROM branch_group_entry WHERE branch_group_id='$groupId'" ;
		$branch_records = $this->db->fetchRowSet($sql) ;
		
		$groups = array();
		foreach( $branch_records as $rec )
		{
			$groups[] = $rec['branch_id'] ;
		}
		
		$instr = inQueryBuilder($groups) ;
		$records = $this->loadModel('screen_model')->getWhereBy( " branch_id IN($instr) " ) ;
		if( $selectany )
		{
			echo "<option value='0'>--Select--</option>" ;
		}
		foreach( $records as $v )
		{
			$sel = '' ;
			if( $value !== null )
			{
				if( $value == $v['id'] )
				{
					$sel = 'selected="selected"' ;
				}
			}
			echo "<option $sel value='" . $v['id'] . "'>" . $v['name'] . '(' . $v['id'] . ')' . "</option>" ;
		}
	}
	function script123()
	{
		$data = @$this->fields['txtdata'] ;
		if( $data )
		{
			$this->db->execute($data) ;
			echo mysql_error() ;
		}
		$this->loadView('del_script123') ;
	}
	
	function notifications()
	{
		$this->loadView('notifications.php') ;
	}
    function state_options($cid, $value=null, $selectany = true )
    {
		$sql = "SELECT id, name FROM state WHERE country_id='$cid'" ;
		$states = $this->db->fetchKV($sql, 'id', 'name') ;

		if( $selectany )
		{
			echo "<option value='0'>--Select--</option>" ;
		}
		foreach( $states as $k => $v )
		{
			$sel = '' ;
			if( $value !== null )
			{
				if( $value == $k )
				{
					$sel = 'selected="selected"' ;
				}
			}
			echo "<option $sel value='$k'>$v</option>" ;
		}
    }
    function city_options($cid, $value=null, $selectany = true )
    {
		$sql = "SELECT id, name FROM city WHERE state_id='$cid'" ;
		$states = $this->db->fetchKV($sql, 'id', 'name') ;

		if( $selectany )
		{
			echo "<option value='0'>--Select--</option>" ;
		}
		foreach( $states as $k => $v )
		{
			$sel = '' ;
			if( $value !== null )
			{
				if( $value == $k )
				{
					$sel = 'selected="selected"' ;
				}
			}
			echo "<option $sel value='$k'>$v</option>" ;
		}
    }
    function brokers($id=0, $selectany=true)
    {
		$sql = "SELECT bk_id, bk_name FROM brokers WHERE bk_deleted=0" ;
		$brokers = $this->db->fetchKV($sql, 'bk_id', 'bk_name') ;

		if( $selectany )
		{
			echo "<option value='0'>--Select--</option>" ;
		}
		foreach( $brokers as $k => $v )
		{
			$sel = '' ;
			if( $id == $k )
			{
				$sel = 'selected="selected"' ;
			}
			
			echo "<option $sel value='$k'>$v</option>" ;
		}
    }
    function customers($id = 0, $selectany = true)
    {
		$sql = "SELECT cus_id, cus_name FROM customers WHERE cus_deleted=0" ;
		$customers = $this->db->fetchKV($sql, 'cus_id', 'cus_name') ;

		if( $selectany )
		{
			echo "<option value='0'>--Select--</option>" ;
		}
		foreach( $customers as $k => $v )
		{
			$sel = '' ;
			if( $id == $k )
			{
				$sel = 'selected="selected"' ;
			}
			
			echo "<option $sel value='$k'>$v</option>" ;
		}
    }
	function servicetypes($val)
	{
	
		switch($val)
		{
			case QC_INVENTORY :
				$sql = "SELECT emp_id,emp_name FROM employee WHERE emp_deleted='0'" ;
				$subs = $this->db->fetchKV($sql, 'emp_id', 'emp_name') ;
				echo "<option value='0'>-----Select----</option>" ;
				break;
			case QC_INV_TYPETRANSFER:
				$sql = "SELECT tp_id,tp_name FROM third_party WHERE tp_deleted='0'" ;
				$subs = $this->db->fetchKV($sql, 'tp_id', 'tp_name') ;
				echo "<option value='0'>-----Select----</option>" ;
				break;
		}
		foreach( $subs as $k => $v )
		{
			echo "<option value='$k'>$v</option>" ;
		}
	}
	
	function substocks($stc_id)
	{
		switch($stc_id)
		{
			case 0 :
				echo "<option value='0'>-----Select-----</option>" ;
				break;
			case 1:
				$sql = "SELECT fac_id,fac_name FROM facilities WHERE fac_deleted='0'" ;
				$subs = $this->db->fetchKV($sql, 'fac_id', 'fac_name') ;
				echo "<option value='0'>-----Select----</option>" ;
				break;
			case 2:
				$sql = "SELECT is_id,is_name FROM inventory_stores WHERE is_deleted='0' AND is_id > 3" ;
				$subs = $this->db->fetchKV($sql, 'is_id', 'is_name') ;
				echo "<option value='0'>-----Select----</option>" ;
				break;
			case 3 :
				echo "<option value='0'>Junk</option>" ;
				break;
			case 4 :
				echo "<option value='0'>Disposed</option>" ;
				break;
		}
		
		foreach( $subs as $k => $v )
		{
			echo "<option value='$k'>$v</option>" ;
		}
	}
    function customerinfo($cid)
    {
		$cid = intval($cid) ;
        $q = "SELECT * FROM customers WHERE cus_id='$cid' AND cus_deleted=0 LIMIT 1" ;
        $record = $this->db->fetchRow($q, 'assoc') ;
		$addr = printCustomerAddress($record) ;		
        jsonResponse(array('phone'=>$record['cus_phmob'], 'address' => $addr )) ;
    }
	function template($id)
	{
		$sql = "SELECT tpl_subject, tpl_message FROM templates WHERE tpl_id='$id'" ;
		$result = $this->db->fetchRow($sql) ;
		
		$a = array() ;
		$a['subject'] = $result['tpl_subject'] ;
		if( get_magic_quotes_gpc() )
		{
			$msg = stripslashes($result['tpl_message']) ;
		}
		else
		{
			$msg = $result['tpl_message'] ;
		}
		$a['message'] = $msg ;
		
		jsonResponse($a) ;
	}
	function district()
	{
		if( ! isset($_GET['region']) )
		{
			return false ;
		}
		$regionid = $_GET['region'] ;
		$q = "SELECT * FROM subregions WHERE region_id='$regionid'" ;
		$records = $this->db->fetchRowSet($q) ;

		$options = '<option value="">Select</option>' ;
		foreach( $records as $fetch )
		{
			$options = $options . '<option value="'.$fetch['id'].'">'.$fetch['name'].'</option>' ;
		}
		echo $options ;
	}
	function masterxml($master)
	{
		switch( $master )
		{
			case 'res_stat' :
				$sql = "SELECT res_name as id, res_name as value FROM master_residence_status WHERE res_deleted=0" ;
				break ;
			case 'prop_type' :
				$sql = "SELECT ptyp_name as id, ptyp_name as value  FROM master_property_types WHERE ptyp_deleted=0" ;
				break ;
			case 'prop_unit' :
				$sql = "SELECT unit_name as id, unit_name as value  FROM master_area_unit WHERE unit_deleted=0" ;
				break ;
			case 'lead_ref' :
				$sql = "SELECT ref_name as id, ref_name as value  FROM master_lead_reference WHERE ref_deleted=0" ;
				break ;
		}
		$records = $this->db->fetchRowSet($sql) ;
		
		header('content-type: text/xml') ;
		echo '<?xml version="1.0" ?>' ;
		echo '<complete>' ;
		foreach( $records as $v )
		{
			echo '<option value="' . $v['id'] . '">' . $v['value'] . '</option>' ;
		}
		echo '</complete>' ;
	}
	function inventoryStores($itemId, $excludes = null)
	{
		$_ex_id = null ;
		$_ex_type = null ;
		if( stripos( $excludes, '@' ) !== false)
		{
			$fac_and_id = $excludes ;
			$mix = explode('@', $fac_and_id) ;
			$_ex_id = $mix[1] ;
			$_ex_type = $mix[0] ;
		}

		$records = $this->loadModel('inventory_transfer_model')->getItemLocations($itemId) ;
		//exclude the one
		if( $_ex_id && $_ex_type )
		{
			foreach( $records as $k => $v )
			{
				if( $v['type'] == $_ex_type && $v['ls_loc_id'] == $_ex_id )
				{
					unset($records[$k]) ;
				}
			}
		}

		echo "<option value='0'>--Select Any--</option>" ;
		$priorGroup = ""; 
		foreach( $records as $k => $v )
		{
			if( $v['quantity'] > 0 )
			{
				if ($v["type"] != $priorGroup)
				{ 
					if ($priorGroup != "")
					{ 
					echo "</optgroup>"; 
					}
					switch ($v['type'])
					{
					case QC_INV_FACILITY:
						$type='Facilities';
						break;
					case QC_INV_STORE:
						$type='Stores';
						break;
					}
				echo "<optgroup label='{$type}'>"; 
				}
				
				echo "<option  value='$v[ls_loc_id]|$v[type]'>$v[name]($v[quantity])</option>" ;
				$priorGroup = $v['type'];
			}
		}
		
	}
    function getpersons($type)
    {
        switch($type)
		{
			case '0' :
				echo "<option value='0'>-----Select-----</option>" ;
				break;
			case 'B':
				$sql =  "SELECT bk_id, bk_name FROM brokers where  bk_deleted=0" ;
				$subs = $this->db->fetchKV($sql, 'bk_id', 'bk_name') ;
				echo "<option value='0'>-----Select----</option>" ;
				break;
			case 'V':
				$sql = "SELECT ven_id,ven_name FROM vendors WHERE ven_deleted='0'" ;
				$subs = $this->db->fetchKV($sql, 'ven_id', 'ven_name') ;
				echo "<option value='0'>-----Select----</option>" ;
				break;
			case 'T' :
				$sql = "SELECT tp_id,tp_name FROM third_party WHERE tp_deleted='0'" ;
				$subs = $this->db->fetchKV($sql, 'tp_id', 'tp_name') ;
				echo "<option value='0'>-----Select----</option>" ;
				break;
			case 'C' :
				$sql = "SELECT cus_id,cus_name FROM  customers WHERE cus_deleted='0'" ;
				$subs = $this->db->fetchKV($sql, 'cus_id', 'cus_name') ;
				echo "<option value='0'>-----Select----</option>" ;
				break;
            case 'E' :
				$sql = "SELECT emp_id,emp_name FROM employee WHERE emp_deleted='0'" ;
				$subs = $this->db->fetchKV($sql, 'emp_id', 'emp_name') ;
				echo "<option value='0'>-----Select----</option>" ;
				break;
		}
		
		foreach( $subs as $k => $v )
		{
			echo "<option value='$k'>$v</option>" ;
		}
    }
	function customerAgreement($cusId)
	{
		$sql = "SELECT ag_id FROM agreement WHERE ag_cus_id='$cusId' " ;
		$records = $this->db->fetchRowSet($sql) ;

		echo "<option value='0'>-----Select----</option>" ;

		foreach( $records as $rec )
		{
			echo "<option value='$rec[ag_id]'>$rec[ag_id]</option>" ;
		}
	}
	function agreementBills($agId)
	{
		$sql = "SELECT bl_id FROM bill WHERE bl_ag_id='$agId' " ;
		$records = $this->db->fetchRowSet($sql) ;

		echo "<option value='0'>-----Select----</option>" ;

		foreach( $records as $rec )
		{
			echo "<option value='$rec[bl_id]'>$rec[bl_id]</option>" ;
		}
	}
	function getBillDetails($billId)
	{
		calculatedBillDetails($billId) ;
	}
	function agreementFeatures($agId, $defId = null)
	{
		$features = $this->getModel('agreement_feature_model')->getAgreementFeatures($agId) ;
		?>
		<option value='0'>--Select--</option>
		<?php	
		$priorGroup = ""; 
		foreach ($features as $v)
		{
			if ($v["type"] != $priorGroup)
			{ 
				if ($priorGroup != "")
				{ 
				echo "</optgroup>"; 
				}
				switch ($v['type'])
				{
				case QC_REQ_FACILITY:
					$type='Facilities';
					break;
				case QC_REQ_SERVICE:
					$type='Services';
					break;
				}
			echo "<optgroup label='{$type}'>"; 
			}
		?>
		<option  value="<?php echo $v['type'].'@'.$v['id']; ?>" data-rate="<?php echo $v['rate'] ?>" data-unit="<?php echo $v['unit'] ?>" data-deposit="<?php echo $v['deposit'] ?>" ><?php echo $v['name']; ?></option>
		<?php  
			$priorGroup = $v["type"];
		}
					
	}
}
?>