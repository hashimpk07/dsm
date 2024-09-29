<?php
class Transfer extends Controller
{
	public function __construct(){

		$acls = array(
			'allow' => array(
						'S, E' => '*' ),
			'deny' => array(),
			'order' => 'AD' //Allow, Then Deny (Options are "DA" or "AD")
		);
		$this->acl($acls) ;

		$this->loadModel('transfer_detail_model') ;
		$this->loadModel('transfer_model') ;
		$this->loadModel('stock_model') ;

		parent::__construct();
		$this->loadLibrary('alldata') ;
	}
	/**
	 * Default function
	 */
	function index()
	{
		parent::defIndex() ;
	}
	/**
	 * All search reach here
	 */
	function search()
	{
		$this->listtable("") ;
	}
	/*
	 * Ajax page view
	 */
	function page($arg = 'normal')
	{
		$this->listall($arg);
	}
	function listall($arg="")
	{
		$class = strtolower(get_class($this));
		$view = $class . '.php';
		ob_start() ;
		$this->listtable($arg) ;
		$vars['listtable'] = ob_get_clean() ;

		return $this->loadView($view, $vars);
	}
	/**
	 * Validation function for add and edit.
	 * 
	 * @param string 'e' for edit, 'a' for add
	 * @return array validation results..
	 */
	function listtable($arg="",$page=1)
	{
		$this->loadLibrary('pagination.php') ;
		$filter = $this->input->request('searchq') ;
		$daterange = stripslashes( $this->input->request('searchq-daterange') ) ;

		$datea = array() ;
		if( $daterange )
		{
			$datea = json_decode($daterange, true) ;
		}
		
		if( ! $filter )
		{
			$filter = $this->input->request('hid-searchq') ;
		}
		$cond = '' ;
		if( $filter )
		{
			$cond .= "AND (f.name LIKE '%$filter%' OR c.name LIKE '%$filter%' OR i.name LIKE '%$filter%' OR e.name LIKE '%$filter%' OR l2.name LIKE '%$filter%' OR l1.name LIKE '%$filter%' OR t1.name LIKE '%$filter%' OR t.id = '$filter' )" ;
		}
		if(count($datea) == 2 )
		{
			$cond .= " AND (DATE(dt) BETWEEN  '" . $datea['start'] . " 00:00'  AND '". $datea['end'] . " 23:59') " ;
		}
		
		$dept_loc_id_from = $this->input->request('searchq-department1');
		$dept_loc_id_to = $this->input->request('searchq-department2');
		if( $dept_loc_id_from )
		{
			list($typo, $id) = explode('@', $dept_loc_id_from) ;
			if( strtoupper($typo) == 'D' )
			{
				$cond .= " AND f.id='$id' " ;
			}
			else if( strtoupper($typo) == 'L' )
			{
				$cond .= " AND l1.id='$id' " ;
			}
		}
		if( $dept_loc_id_to )
		{
			list($typo, $id) = explode('@', $dept_loc_id_to) ;
			if( strtoupper($typo) == 'D' )
			{
				$cond .= " AND t1.id='$id' " ;
			}
			else if( strtoupper($typo) == 'L' )
			{
				$cond .= " AND l2.id='$id' " ;
			}
		}
		//{sort
		$sort = '' ;
		if( @$this->input->request['searchq-col'] || @$this->input->request['hid-searchq-col'] )
		{
			$sort = ' ORDER BY ' . ( ($this->input->request['searchq-col']) ? $this->input->request['searchq-col'] : @$this->input->request['hid-searchq-col'] ) ;
			if( (@$this->input->request['searchq-sort'] == 'desc') || (@$this->input->request['hid-searchq-sort'] == 'desc') )
			{
				$sort .= ' DESC ' ;
			}
			else
			{
				$sort .= ' ASC ' ;
			}
		}
		if( ! $sort )
		{
			$sort = ' ORDER BY t.id DESC ' ;
		}

		//location restriction {
		global $QFA ;
		if( $QFA['location.id'] )
		{
			$cond .= " AND f.id IN(" . $QFA['location.inquery'] . ") AND t1.id IN(" . $QFA['location.inquery'] . ") " ;
		}
		//}

		$group = " GROUP BY t.id";
		//}
		
		$sql = "SELECT t.*, f.name as from_department, t1.name as to_department, e.name as employee, l1.name AS from_location, l2.name AS to_location FROM transfer t 
				INNER JOIN department f ON f.id = t.from_department_id
				INNER JOIN department t1 ON t1.id = t.to_department_id
				
				LEFT JOIN transfer_detail td ON td.transfer_id = t.id
				LEFT JOIN item i ON i.id = td.item_id
				LEFT JOIN category c ON c.id = i.category_id
				
				LEFT JOIN location l1 ON l1.id=f.location_id
				LEFT JOIN location l2 ON l2.id=t1.location_id
				LEFT JOIN employee e ON e.id = t.createby 
				WHERE 1 $cond $group $sort " ;
		
		$sqlcnt = "SELECT COUNT(*) as cnt FROM(SELECT t.id FROM transfer t 
				INNER JOIN department f ON f.id = t.from_department_id
				INNER JOIN department t1 ON t1.id = t.to_department_id
				
				LEFT JOIN transfer_detail td ON td.transfer_id = t.id
				LEFT JOIN item i ON i.id = td.item_id
				LEFT JOIN category c ON c.id = i.category_id

				LEFT JOIN location l1 ON l1.id=f.location_id
				LEFT JOIN location l2 ON l2.id=t1.location_id
				LEFT JOIN employee e ON e.id = t.createby 
				WHERE 1 $cond $group ) AS a" ;

			 $header_field=get_class($this);
		$url = siteUrl('transfer/listtable/' . $arg . '/') ;
		$this->vars['pager_url'] = $url ;
		if( $this->ifCsvExport() )
		{
			$order = array(
				'dt' => 'Date',
				'from_department'=>'Transfer From',
				'to_department'=>'Transfer To',
				'employee'=>'Transfer By',
			) ;

			$filename = 'transfer' . Date('Ymdhi') . '.csv' ;
			$this->exportCsv($sql, $order, $header_field, $header=null, $filename) ;
			return;
		}
		$sqlx = "SELECT MAX(id) FROM transfer" ;
		$max = $this->db->scalarField($sqlx) ;
		$this->vars['lastid'] = $max ; 
		$result = $this->pagination->pager($sql, $sqlcnt, $url, 'idListArea' . get_class($this), $page) ;

		//render page with default template file.
		parent::defListtable($result) ;
	}
	function validate($mode)
	{
		$errors = array();
		$errors['eFromDepartment'] = '';
		$errors['eToDepartment'] = '';
		$errors['eItem'] = '';
		$errors['eTransferQty'] = '';

		if ($mode == 'add' || $mode == 'edit')
		{
			if( @$this->fields['slFromDepartment'] == '0' )
			{
				$errors['eFromDepartment'] = 'From Location not selected';
			}
			if( @$this->fields['slToDepartment'] == '0' )
			{
				$errors['eToDepartment'] = 'To Location not selected';
			}
			//Source and destinatio matches ?
			if( @$this->fields['slFromDepartment'] == @$this->fields['slToDepartment'] )
			{
				$errors['eToDepartment'] = 'From & To Location must be different';
			}

			//get From Type {
			$fromDeptId = @$this->fields['slFromDepartment'] ;
			$toDeptId = @$this->fields['slToDepartment'] ;
			$sqlde = "SELECT type FROM department WHERE id='$fromDeptId'" ;
			$deptType = $this->db->scalarField($sqlde) ;
			$sqlde2 = "SELECT type FROM department WHERE id='$toDeptId'" ;
			$deptType2 = $this->db->scalarField($sqlde2) ;
			//}
			if( $deptType2 == QC_DEPT_TYPE_NEW )
			{
				$errors['eToDepartment'] = 'Invalid department selected.';
			}

			if( isset($this->fields['txtTransferQty']) )
			{
				$trnqty = $this->fields['txtTransferQty'];

				
				//N for new stock
				if( $deptType != QC_DEPT_TYPE_NEW )
				{
					//sum up quantity {
					$qtsum = array() ;
					$from = $this->fields['slFromDepartment'] ;
					foreach( $trnqty as $k => $v )
					{
						$itmId = $this->fields['slItem'][$k];

						if( ! isset($qtsum[$from][$itmId]) )
						{
							if( $mode == 'edit' )
							{
								$editId = $this->fields['editId'] ;
								$available = $this->transfer_model->getQuantityEdit($itmId, $from, $editId);
							}
							else
							{
								$available = $this->transfer_model->getQuantity($itmId, $from);
							}
							$qtsum[$from][$itmId] = array('avail'=> $available, 'request' => $v) ;						
						}
						else
						{
							$qtsum[$from][$itmId]['request'] += $v ;
						}
					}
					foreach($qtsum as $itmset )
					{
						foreach( $itmset as $v )
						{
							if( $v['request'] > $v['avail'] )
							{
								$errors['eTransferQty'] = 'Quantity exceed stock';
							}
						}
					}
				}
				//}

				$nameset = array() ;
				foreach ($trnqty as $key => $value)
				{
					$istring = @$this->fields['slItem'][$key] ;
					if( stripos($istring, 'QRDA@') === 0 )
					{
						$keyx = str_ireplace('QRDA@', '', $istring) ;
						if( ! isset($nameset[$keyx]) )
						{
							$nameset[$keyx] = 0 ;
						}
						$nameset[ $keyx ] ++ ;
					}
					else if( stripos($istring, 'QRDC@') === 0 )
					{
						$keyx = str_ireplace('QRDC@', '', $istring) ;
						if( ! isset($nameset[$keyx]) )
						{
							$nameset[$keyx] = 0 ;
						}
						$nameset[ $keyx ] ++ ;
					}
						
					if (!@$this->fields['slCategory'][$key])
					{
						$errors['eCategory'] = 'Category not specified' ;
					}
					if (!@$this->fields['slItem'][$key])
					{
						$errors['eItem'] = 'Item not selected';
					}
					if (!@$this->fields['txtTransferQty'][$key])
					{
						$errors['eTransferQty'] = 'Quantity not specified';
					}
					if (!empty($this->fields['txtTransferQty'][$key]))
					{
						if (is_numeric(@$this->fields['txtTransferQty'][$key]) == false)
						{
							$errors['eTransferQty'] = 'Inavlid quantity';
						}
					}
				}
				//
				if( count(@$nameset) > 0 )
				{
					if( max($nameset) > 1 )
					{
						$errors['eItem'] = 'Duplicate items' ;
					}
				}
			}
			else
			{
				$errors['eItem'] = 'please add  atleast one row';
			}
		}

		return $errors;
	}
	function getItemId($itemNameOrId, $categoryId)
	{
		$str1 = intval($itemNameOrId) ;
		$str2 = $itemNameOrId ;
		if( strcmp($str1, $str2) === 0 )
		{
			return $itemNameOrId ;
		}
		
		if(stripos($itemNameOrId, 'QRDA@') === 0 )
		{
			$itemNameOrId = urldecode( str_ireplace('QRDA@', '', $itemNameOrId) ) ;

			$data = array(
				'name' => $itemNameOrId,
				'category_id' => $categoryId,
				'type' => 'A',
			) ;
			$this->loadModel('item_model')->insert($data) ;
			return $this->db->getLastInsertId() ;
		}		
		else if(stripos($itemNameOrId, 'QRDC@') === 0 )
		{
			$itemNameOrId = urldecode( str_ireplace('QRDC@', '', $itemNameOrId) ) ;

			$data = array(
				'name' => $itemNameOrId,
				'category_id' => $categoryId,
				'type' => 'C',
			) ;
			$this->loadModel('item_model')->insert($data) ;
			return $this->db->getLastInsertId() ;
		}
		return $itemNameOrId ;
	}
	function getItemType($id)
	{
		$sql = "SELECT type FROM item WHERE id='$id' " ;
		return $this->db->scalarField($sql) ;
	}
	function getDeptType($id)
	{
		$sql = "SELECT type FROM department WHERE id='$id' " ;
		return $this->db->scalarField($sql) ;
	}
	/**
	 * Function to handle add form submition.
	 * 
	 * @return boolean true on success
	 */
	private function onAdd()
	{
		$errors = $this->validate('add') ;
		//has any error ?
		if( countReal($errors) > 0 )
		{
			//then stop here..
			return new JStatus(false, 'Please fix validation errors', $errors) ;
			//--- END: ----
		}
		$this->db->beginTrans() ;
		
		$qtys = $this->fields['txtTransferQty'] ;
		
		if( count($qtys) > 0 )
		{
			//transfer.. {
			$transfer = array(
				'from_department_id' => $this->fields['slFromDepartment'],
				'to_department_id' => $this->fields['slToDepartment'],
				'dt' => mysqlDateNow(),
				'createby' => $this->session->get('usr_id'),
				'createdt' => mysqlDateNow(),
			) ;
			$this->transfer_model->insert($transfer) ;
			$itId = $this->db->getLastInsertId() ;
			//}
			
			foreach ($qtys as $key => $value) 
			{
				$itemId = $this->getItemId($this->fields['slItem'][$key], $this->fields['slCategory'][$key]) ;
				$tranferQty = $this->fields['txtTransferQty'][$key] ;

				//details
				$detOne = array(
					'item_id' => sqlNullableKey($itemId),
					'transfer_id' => $itId,
					'quantity' => $tranferQty,
				);
				$this->transfer_detail_model->insert($detOne) ;	

				$transferTo = $transfer['to_department_id'] ;
				//for consumable no destination. {
				if( $this->getItemType($itemId) == 'C' && $this->getDeptType($transferTo) == 'J' )
				{
					$transferTo = null ;
				}
				//}
				//update stock..
				$this->stock_model->updateLocationStock($transferTo, $detOne['item_id'], $detOne['quantity'], $transfer['from_department_id'] ) ;
			}

			$this->db->commitTrans() ;
			return new JStatus(true, 'Items transferred successfuly',array('__id' => $itId )) ;
		}

		$this->db->rollbackTrans() ;
		return new JStatus(false, 'Unable to save', array('__id' => 0 )) ;
	}
	/**
	 * Shows add form
	 */
	function add()
	{
		if( $this->input->post('btnSubmit') )
		{
			$jstat = $this->onAdd() ;
			if( $jstat->status )
			{
				ob_start() ;
				if( $this->getArg('contentAreaClicked') == 'idContentAreaSmall' )
				{
					$this->view($jstat->data['__id']) ;					
				}
				else
				{
					$this->page() ;
				}

				$jstat->data['idContentAreaBig'] = ob_get_clean() ;
			}

			$this->statusResponse($jstat) ;
			return ;
		}
		
		$this->vars['stock'] = $this->getModel('stock_model')->get() ;

		$this->vars['items'] = $this->getModel('item_model')->getWhere(array('deleted' => 0));
		$this->vars['categories'] = $this->getModel('category_model')->getCategory() ;
		$this->vars['locations'] = $this->getModel('location_model')->getLocation();
		$this->vars['departments'] = $this->getModel('department_model')->getdepartment();

		$this->vars['mode'] = 'add' ;

		$this->vars['url'] = siteUrl('transfer/add' ) ;
		parent::defAdd() ;
	}
	/**
	 * Display indivitual details
	 */
	function view($id, $type = 'normal')
	{
		//transfer info
		$info = $this->transfer_model->getInfo($id) ;
		$rec = $this->transfer_model->getInventoryItems($id) ;
		$this->loadView('transfer_view.php',array('result' => $rec, 'info' => $info));
	}

	private function onEdit()
	{
		$errors = $this->validate('edit') ;
		//has any error ?
		if( countReal($errors) > 0 )
		{
			//then stop here..
			return new JStatus(false, 'Please fix validation errors', $errors) ;
			//--- END: ----
		}
		$this->db->beginTrans() ;

		$qtys = $this->fields['txtTransferQty'] ;

		if( count($qtys) > 0 )
		{
			//transfer.. {
			$transfer = array(
				'from_department_id' => $this->fields['slFromDepartment'],
				'to_department_id' => $this->fields['slToDepartment'],
				'dt' => mysqlDateNow(),
				'createby' => $this->session->get('usr_id'),
				'createdt' => mysqlDateNow(),
			) ;
			$editId = $this->fields['editId'] ;
			$this->transfer_model->update($transfer, array('id' => $editId)) ;
			$itId = $editId; 
			//}
			//Revert existing stock {
			$sqlexist = "SELECT td.*, t.* FROM transfer t 
						INNER JOIN transfer_detail td ON t.id=td.transfer_id WHERE t.id='$editId' " ;
			$existrec = $this->db->fetchRowSet($sqlexist) ;
			foreach( $existrec as $rec )
			{
				$transferFrom = $transfer['to_department_id'] ;
				//for consumable no source. {
				if( $this->getItemType($rec['item_id']) == 'C' && $this->getDeptType($transferFrom) == 'J' )
				{
					$transferFrom = null ;
				}
				//}

				$this->stock_model->updateLocationStock($rec['from_department_id'], $rec['item_id'], $rec['quantity'], $transferFrom ) ;
			}
			//delete existing records..
			$sqld = "DELETE FROM transfer_detail WHERE transfer_id='$editId'" ;
			$this->db->execute($sqld) ;
			//}
			foreach ($qtys as $key => $value) 
			{
				$itemId = $this->getItemId($this->fields['slItem'][$key], $this->fields['slCategory'][$key]) ;
				$tranferQty = $this->fields['txtTransferQty'][$key] ;

				//details
				$detOne = array(
					'item_id' => sqlNullableKey($itemId),
					'transfer_id' => $itId,
					'quantity' => $tranferQty,
				);
				$this->transfer_detail_model->insert($detOne) ;	

				$transferTo = $transfer['to_department_id'] ;
				//for consumable no source. {
				if( $this->getItemType($detOne['item_id']) == 'C' && $this->getDeptType($transferTo) == 'J' )
				{
					$transferTo = null ;
				}
				//}
				//update stock..
				$this->stock_model->updateLocationStock($transferTo, $detOne['item_id'], $detOne['quantity'], $transfer['from_department_id'] ) ;
			}

			$this->db->commitTrans() ;
			return new JStatus(true, 'Items transferred successfuly',array('__id' => $itId )) ;
		}

		$this->db->rollbackTrans() ;
		return new JStatus(false, 'Unable to save', array('__id' => 0 )) ;
	}
	/**
	 * Shows add form
	 */
	function edit($id)
	{
		if( $this->input->post('btnSubmit') )
		{
			//$jstat = $this->onEdit() ;
			//TODO: code for reversal {
			$jstat = $this->onAdd() ;
			//}
			if( $jstat->status )
			{
				ob_start() ;
				if( $this->getArg('contentAreaClicked') == 'idContentAreaSmall' )
				{
					$this->view($jstat->data['__id']) ;					
				}
				else
				{
					$this->page() ;
				}

				$jstat->data['idContentAreaBig'] = ob_get_clean() ;
			}

			$this->statusResponse($jstat) ;
			return ;
		}

		$this->vars['info'] = $this->transfer_model->getInfo($id) ;

		////TODO: Code Fro Reversal: {{
		//Revert for reverse transfer 
		$t = $this->vars['info']['to_department_id'] ;
		$this->vars['info']['to_department_id'] = $this->vars['info']['from_department_id'] ;
		$this->vars['info']['from_department_id'] = $t ;
		//
		$t = $this->vars['info']['to_location_id'] ;
		$this->vars['info']['to_location_id'] = $this->vars['info']['from_location_id'] ;
		$this->vars['info']['from_location_id'] = $t ;
		//
		$t = $this->vars['info']['to_department'] ;
		$this->vars['info']['to_department'] = $this->vars['info']['from_department'] ;
		$this->vars['info']['from_department'] = $t ;
		$this->vars['mode2'] = 'reverse' ;
		//
		//}}
		$this->vars['records'] = $this->transfer_detail_model->transferDetails($id) ;
		$this->vars['stock'] = $this->getModel('stock_model')->get() ;
		$this->vars['items'] = $this->getModel('item_model')->get() ;
		$this->vars['categories'] = $this->getModel('category_model')->get() ;
		$this->vars['locations'] = $this->getModel('location_model')->get() ;
		$this->vars['departments'] = $this->getModel('department_model')->get() ;

		$this->vars['mode'] = 'edit' ;
		$this->vars['editId'] = $id ;

		$this->vars['url'] = siteUrl('transfer/edit/' . $id ) ;
		parent::defAdd() ;
	}	
	function delete($id, $silent = false )
	{
		$status = false ;
		//Revert existing stock {
		$sqlexist = "SELECT td.*, t.* FROM transfer t 
					INNER JOIN transfer_detail td ON t.id=td.transfer_id WHERE t.id='$id' " ;
		$existrec = $this->db->fetchRowSet($sqlexist) ;
		foreach( $existrec as $rec )
		{
			$this->stock_model->updateLocationStock($rec['from_department_id'], $rec['item_id'], $rec['quantity'], $rec['to_department_id'] ) ;
		}
		//delete existing records..
		$sqld = "DELETE FROM transfer_detail WHERE transfer_id='$id'" ;
		if( $this->db->execute($sqld) )
		{
			$sqldm = "DELETE FROM transfer WHERE id='$id'" ;
			if( $this->db->execute($sqldm) )
			{
				$status = true ;
			}
		}
		//}

		if( ! $silent )
		{
			$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'Deleted successfully' : 'Unable to delete details'), array('_id' => $id)) ;
		}
		return $status;
	}
}