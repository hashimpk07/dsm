<?php
class Item extends Controller
{
	public function __construct()	{

		$acls = array(
			'allow' => array(
						'*' => '*' ),
			'deny' => array(),
			'order' => 'AD' //Allow, Then Deny (Options are "DA" or "AD")
		);
		$this->acl($acls) ;

		parent::__construct();

		/* @var $this->brokers_model Brokers_model */
		$this->loadModel('item_model') ;
	}
	/**
	 * Default function
	 */
	function index()
	{
		//request will be routed to display.
		parent::defIndex() ;
	}
	/**
	 * All search reach here
	 */
	function search()
	{
		$this->listtable() ;
	}
	/*
	 * Ajax page view
	 */
	function page()
	{
		$this->listall();
	}
	/**
	 * Display view without design layout.
	 */
	function listall()
	{
		parent::defListall() ;
	}
	/**
	 * Method to retrieve records list table.
	 * 
	 * @param int $page page no.
	 */
	function listtable($page=1)
	{
		$this->loadLibrary('pagination.php') ;
		$filter = $this->input->request('searchq') ;
		if( ! $filter )
		{
			$filter = $this->input->request('hid-searchq') ;
		}
		$cond = '' ;
		if( $filter )
		{
			$cond = " AND (i.name LIKE '%$filter%' OR i.type LIKE '%$filter%' OR  c.name LIKE '%$filter%' )" ;
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
			$sort = ' ORDER BY i.id DESC ' ;
		}

		$sql = "SELECT i.id,i.type,i.name,c.name as categoryname FROM item i
				LEFT JOIN category c ON c.id=i.category_id
				WHERE i.deleted='0' $cond $sort" ;
		$sqlcnt = "SELECT COUNT(*) as total FROM item i 
					LEFT JOIN category c ON c.id=i.category_id
					WHERE i.deleted='0' $cond" ;
		$url = siteUrl('item/listtable/') ;

        $this->vars['pager_url'] = $url ;
			if( $this->ifCsvExport() )
											{
												$order = array(
												'name' => 'Item',
												'name'=>'Category',
												) ;

												$this->exportCsv($sql, $order) ;
												return;
            
											}
		
		
		

		$result = $this->pagination->pager($sql, $sqlcnt, $url, 'idListArea' . get_class($this), $page) ;
		//render page with default template file.
		parent::defListtable($result) ;
	}
	/**
	 * Validation function for add and edit.
	 * 
	 * @param string 'e' for edit, 'a' for add
	 * @return array validation results..
	 */
	function validate($mode)
	{
		$errors = array() ;
		$errors['eName'] = '' ;
		$errors['eCategory'] = '' ;
		$errors['eConsumable'] = '' ;
		

		//{ db check conditions
		if( $mode == 'edit' )
		{
			$editId = $this->getArg('editId') ;
			$eCond = " ( name = '" . $this->fields['txtName'] . "' AND id != '$editId' AND deleted = 0 ) " ;
			
		}
		else if( $mode == 'add' )
		{
			$eCond = " ( name = '" . $this->fields['txtName'] . "' AND deleted = 0 ) " ;
			
		}
		//}

		//basic test cases		
		if( ! @$this->fields['txtName'] )
		{
			$errors['eName'] = 'Name not specified' ;
		}
		
		if(@$this->fields['cbCategory']==0 )
		{
			$errors['eCategory'] = 'Category not selected';
		}
		if(@$this->fields['txtConsume']=='' )
		{
			$errors['eConsume'] = 'Item type not selected' ;
		}
		//db test cases
		if( @$this->fields['txtName'] )
		{
			if( $this->item_model->isExists($eCond) )
			{
				$errors['eName'] = 'Name address already exists' ;
			}
		}
		

		
		return $errors ;
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
		
		$item = array(
			    'name' => $this->fields['txtName'],
				'category_id' => sqlNullableKey($this->fields['cbCategory']),
				'type' =>$this->fields['txtConsume'],
				'createby' =>$this->session->get('usr_id'),
				'updateby'=>$this->session->get('usr_id'),
				'createdt' => mysqlDateTime(Date('Y-m-d H:i:s')),
				'updatedt' => mysqlDateTime(Date('Y-m-d H:i:s'))
		) ;
	
		if( $this->item_model->insert($item) )
		{	
      
			$insId = $this->db->getLastInsertId() ;
			$this->db->commitTrans() ;

			return new JStatus(true, 'Successfully saved', array('__id' => $insId)) ;
			 
		}
		 
		$this->db->rollbackTrans() ;
		return new JStatus(false, 'Unable to save') ;
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
				if( $this->getArg('contentAreaClicked') != 'idPopupSubmit' )
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
			}
			$this->statusResponse($jstat) ;
			return ;
		}
		$this->vars['category']=$this->getModel('category_model')->getCategory();
		
		$this->vars['mode'] = 'add' ;
		$this->vars['url'] = siteUrl('item/add') ;
		parent::defAdd() ;
	}
	/**
	 * On edit submit
	 */
	private function onEdit($id)
	{
		$errors = $this->validate('edit') ;
		//has any error ?
		if( countReal($errors) > 0 )
		{
			//then stop here..
			return new JStatus(false, 'Please fix validation errors', $errors) ;
			//--- END ---
		}

		//get id
        $this->db->beginTrans() ;
		//get current values 
		$data = $this->item_model->getDetails($id) ;

		$item = array(
			    'name' => $this->fields['txtName'],
				'category_id' => sqlNullableKey($this->fields['cbCategory']),
				'type' =>$this->fields['txtConsume'],
				'updateby'=>$this->session->get('usr_id'),
				'updatedt' => mysqlDateTime(Date('Y-m-d H:i:s'))
		) ;
		
		if( $this->item_model->update($item, array('id' => $id)) )
		{	
			$this->db->commitTrans();
			//return status
			return new JStatus(true, 'Item details updated successfully') ;
		}
        $this->db->rollbackTrans();
		return new JStatus(false, 'Unable to update item details') ;
	}
	/**
	 * Edit id
	 * 
	 * @param int $id
	 */
	function edit($id)
	{
		if( $this->input->post('btnSubmit') )
		{
			$jstat = $this->onEdit($id) ;

			if( $jstat->status )
			{
				ob_start() ;
				if( $this->getArg('contentAreaClicked') == 'idContentAreaSmall' )
				{
					$this->view($id) ;					
				}
				else
				{
					$this->page() ;
				}
				$jstat->data['idContentAreaBig'] = ob_get_clean() ;
			}
			$this->statusResponse($jstat) ;
			return false ;
		}

		$this->vars['mode'] = 'edit' ;
		$this->setArg('editId', $id) ;
		$this->vars['url'] = siteUrl('item/edit' . '/' . $id ) ;
		$this->vars['result'] = $this->item_model->getDetails($id) ;
		$this->vars['category']=$this->getModel('category_model')->getCategory() ;

		$this->loadView('item_add.php') ;
	}
	/**
	 * Display indivitual details
	 */
	function view($id)
	{
		$rec = $this->item_model->getDetails($id) ;
		$this->loadView('item_view.php', array('result' => $rec)) ;
	}
	/**
	 * Mark a record as deleted.
	 * 
	 * @param int $id id to remove
	 * @return bool
	 */
	function delete($id, $silent = false )
	{
		// //TODO; check item in use of transfer ?
	/*	if( $this->getModel('invoice_details_model')->isExists(array('idt_itm_id' => $id)) )
		{
			if( ! $silent )
			{
				$this->statusResponse( 'Fail', 'Unable to delete Item, Item exists in a Invoice', array('_id' => $id)) ;
			}
			return false ;
		}*/
		
		if( $this->getModel('transfer_detail_model')->isExists(array('item_id' => $id)) )
		{
			if( ! $silent )
			{
				$this->statusResponse( 'Fail', 'Unable to delete Item, Item transfered to some location', array('_id' => $id)) ;
			}
			return false ;
		}
		$status = $this->item_model->flag('deleted', 1, array('id' => $id)) ;
		if( ! $silent )
		{
			$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'Item deleted successfully' : 'Unable to delete item'), array('_id' => $id)) ;
		}
		return $status;
	}
	/**
	 * Buld action handler
	 * 
	 * @return boolean true on succes
	 */
	function bulkAction()
	{
		//Select action
		$action = @$this->fields['hidbulkaction'] ;

		//Validate bulk action
		if( ! $action )
		{
			$this->statusResponse('FAIL', 'Unknown action') ;
			return false ;
		}

		$items = @$this->fields['cbList'] ;
		if( @count($items) < 1 )
		{
			$this->statusResponse('FAIL', 'There are no items') ;
			return false ;
		}

		//Do bulk action
		$msge = '' ;
		$stat = array('action-s' => 0, 'action-f' => 0) ;
		switch( $action )
		{
			case 'delete' :
				$msgs = '%s item(s) deleted.' ;
				$msge = '%f item(s) not deleted.' ;
				foreach( $items as $v )
				{
					if( $this->delete($v, true) )
					{
						$stat['action-s'] ++ ;
					}
					else
					{
						$stat['action-f'] ++ ;
					}
				}
				break ;
		}

		//Format message
		$msg = '' ;
		$status = 'FAIL' ;
		if( $stat['action-s'] > 0 )
		{
			$msg = str_ireplace('%s', $stat['action-s'], $msgs) ;
			$status = 'OK' ;
		}
		if( $stat['action-f'] > 0 )
		{
			$msg = $msg . ' ' . str_ireplace('%f', $stat['action-f'], $msge) ;
			$status = 'FAIL' ;
		}

		$this->statusResponse($status, $msg) ;
	}
}
?>