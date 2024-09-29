<?php
class Branch_group extends Controller
{
	public function __construct()	{

		$acls = array(
			'allow' => array(
						'*' => '*' ),
			'deny' => array(),
			'order' => 'AD', //Allow, Then Deny (Options are "DA" or "AD")
			'user' => true
		);
		$this->acl($acls) ;

		parent::__construct();

		/* @var $this->brokers_model Brokers_model */
		$this->loadModel('branch_group_model') ;
		$this->loadModel('branch_group_entry_model') ;
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
			$cond = " AND (bg.name LIKE '%$filter%')" ;
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
			$sort = ' ORDER BY bg.name DESC ' ;
		}
		
		$sql = "SELECT bg.id, bg.name FROM branch_group bg 
				WHERE 1 $cond $sort" ;
		
		$sqlcnt ="SELECT COUNT(*) as cnt FROM branch_group bg 
				WHERE 1 $cond" ;

		$url = siteUrl('branch_group/listtable/') ;

        $this->vars['pager_url'] = $url ;
		if( $this->ifCsvExport() )
		{
			$order = array(
				'Branch' => 'branch',
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
		
		//{ db check conditions
		if( $mode == 'edit' )
		{
			$editId = $this->getArg('editId') ;
			$eCond = " ( name = '" . $this->fields['txtName'] . "' AND id != '$editId' ) " ;
			
		}
		else if( $mode == 'add' )
		{
			$eCond = " ( name = '" . $this->fields['txtName'] . "' ) " ;
		}
		//}
		

		//basic test cases		
		if( ! @$this->fields['txtName'] )
		{
			$errors['eName'] = 'Name not specified' ;
		}
		//db test cases
		if( @$this->fields['txtName'] )
		{
			if( $this->branch_group_model->isExists($eCond) )
			{
				$errors['eName'] = 'Name already exists' ;
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
		
		$data = array(
			'name' => $this->fields['txtName']
		) ;

		if( $this->branch_group_model->insert($data) )
		{	
			$insId = $this->db->getLastInsertId() ;
			foreach( $this->fields['cbBranch'] as $one )
			{
				$dataone = array(
					'branch_group_id' => $insId,
					'branch_id' => $one,
				) ;
				$this->branch_group_entry_model->insert($dataone) ;
			}
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
		$this->vars['screens']=$this->getModel('screen_model')->get();
		$this->vars['countries']=$this->getModel('country_model')->get();
		$this->vars['branches'] = $this->getModel( 'branch_model' )->get();


		$this->vars['mode'] = 'add' ;
		$this->vars['url'] = siteUrl('branch_group/add') ;
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

		$data = array(
			'name' => $this->fields['txtName']
		) ;
		
		if( $this->branch_group_model->update($data, array('id' => $id)) )
		{	
			//delete existing entries..
			$sqld = "DELETE FROM branch_group_entry WHERE branch_group_id='$id'" ;
			$this->db->execute($sqld) ;

			foreach( $this->fields['cbBranch'] as $one )
			{
				$dataone = array(
					'branch_group_id' => $id,
					'branch_id' => $one,
				) ;
				$this->branch_group_entry_model->insert($dataone) ;
			}
			$this->db->commitTrans();
			//return status
			return new JStatus(true, 'branch group updated successfully') ;
		}
        $this->db->rollbackTrans();
		return new JStatus(false, 'Unable to update branch group') ;
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

		
		$group_recs = $this->branch_group_entry_model->getWhereBy(array('branch_group_id' => $id)) ;
		$group_ids = array() ;
		foreach( $group_recs as $rec )
		{
			$group_ids[] = $rec['branch_id'] ;
		}
		$this->vars['mode'] = 'edit' ;
		$this->setArg('editId', $id) ;
		$this->vars['url'] = siteUrl('branch_group/edit' . '/' . $id ) ;
		$this->vars['result'] = $this->branch_group_model->getWhereByOne	(array('id'=>$id)) ;
		$this->vars['sel_groups'] = $group_ids ;
		$this->vars['branches'] = $this->getModel('branch_model')->get() ;

		$this->vars['countries']=$this->getModel('country_model')->get();
		$this->vars['screens']=$this->getModel('screen_model')->get();
		
		$this->loadView('branch_group_add.php') ;
	}
	/**
	 * Display indivitual details
	 */
	function view($id)
	{
		$rec = $this->branch_group_model->getWhereByOne( array('id' => $id) ) ;
		$entries = $this->branch_group_entry_model->getEntries($id) ;
		$this->loadView('branch_group_view.php', array('result' => $rec, 'entries' => $entries)) ;
	}
	/**
	 * Mark a record as deleted.
	 * 
	 * @param int $id id to remove
	 * @return bool
	 */
	function delete($id, $silent = false )
	{
		$where = array(
			'id' => $id 
		);
		$where_entries = array(
			'branch_group_id' => $id 
		);

		$status = false ;
		$this->db->beginTrans() ;
		if( $this->branch_group_model->delete($where) )
		{
			$this->branch_group_entry_model->delete($where_entries) ;
			$this->db->commitTrans() ;
			$status = true ;
		}
		
		if( ! $silent )
		{
			$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'Branch group deleted successfully' : 'Unable to delete branch group'), array('_id' => $id)) ;
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

		$branch = @$this->fields['cbList'] ;
		if( @count($branch) < 1 )
		{
			$this->statusResponse('FAIL', 'There are no branch') ;
			return false ;
		}

		//Do bulk action
		$msge = '' ;
		$stat = array('action-s' => 0, 'action-f' => 0) ;
		switch( $action )
		{
			case 'delete' :
				$msgs = '%s branch group(s) deleted.' ;
				$msge = '%f branch group(s) not deleted.' ;
				foreach( $branch as $v )
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