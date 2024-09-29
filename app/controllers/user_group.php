<?php
class User_group extends Controller
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
		$this->loadModel('user_group_model') ;
		$this->loadModel('privilege_model') ;
		$this->loadModel('privilege_group_entry_model') ;
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
			$cond = " AND (b.name LIKE '%$filter%' OR b.name LIKE '%$filter%')" ;
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
			$sort = ' ORDER BY ug.id DESC ' ;
		}

		$sql = "SELECT * FROM user_group ug
				WHERE ug.deleted='0' $cond $sort" ;
		
		$sqlcnt ="SELECT COUNT(*) as cnt FROM user_group ug
				WHERE ug.deleted='0' $cond" ;

		$url = siteUrl('user_group/listtable/') ;

        $this->vars['pager_url'] = $url ;
		if( $this->ifCsvExport() )
		{
			$order = array(
				'Name' => 'name',
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

		//db test cases
		if( @$this->fields['txtName'] )
		{
			if( $this->user_group_model->isExists($eCond) )
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
		
		$ug = array(
			    'name' => $this->fields['txtName'],
		) ;

		if( $this->user_group_model->insert($ug) )
		{	
			$insId = $this->db->getLastInsertId() ;

			//insert privileges
			$privileges = $this->fields['cbPrivilege'] ;
			foreach( $privileges as $priv )
			{
				$pa = array(
					'group_id' => $insId,
					'privilege_id' => $priv,
				) ;
				
				$this->privilege_group_entry_model->insert($pa) ;
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

		$this->vars['mode'] = 'add' ;
		$this->vars['privileges'] = $this->privilege_model->get() ;
		$this->vars['url'] = siteUrl('user_group/add') ;
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
		
		$ug = array(
			    'name' => $this->fields['txtName'],
		) ;

		if( $this->user_group_model->update($ug, array('id' => $id)) )
		{	
			//delte all
			$this->privilege_group_entry_model->delete(array('group_id' => $id)) ;

			//insert all selecetd
			$privileges = $this->fields['cbPrivileges'] ;
			foreach( $privileges as $priv )
			{
				$pa = array(
					'group_id' => $id,
					'privilege_id' => $priv,
				) ;
				
				$this->privilege_group_entry_model->insert($pa) ;
			}
			$this->db->commitTrans() ;

			return new JStatus(true, 'Successfully saved', array('__id' => $insId)) ;
		}
		 
		$this->db->rollbackTrans() ;

		return new JStatus(false, 'Unable to update user_group details') ;
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
		$this->vars['url'] = siteUrl('user_group/edit' . '/' . $id ) ;
		$this->vars['result'] = $this->user_group_model->getWhereByOne(array('id' =>$id)) ;
		$this->vars['privileges'] = $this->privilege_model->get() ;
		$this->vars['privilege_group_entrys'] = $this->privilege_group_entry_model->getWhereBy( array('group_id' => $id) ) ;
		$this->vars['screens']=$this->getModel('screen_model')->get() ;

		$this->loadView('user_group_add.php') ;
	}
	/**
	 * Display indivitual details
	 */
	function view($id)
	{
		$rec = $this->user_group_model->getWhereByOne(array('id'=>$id)) ;
		$privilege_group_entrys = $this->privilege_group_entry_model->getWhereBy(array('group_id'=>$id)) ;
		$privileges = $this->privilege_model->get() ;
		
		$data = array() ;
		$data['result'] = $rec ;
		$data['privilege_group_entrys'] = $privilege_group_entrys ;
		$data['privileges'] = $privileges ;
		$this->loadView('user_group_view.php', $data ) ;
	}
	/**
	 * Mark a record as deleted.
	 * 
	 * @param int $id id to remove
	 * @return bool
	 */
	function delete($id, $silent = false )
	{
		$status = $this->user_group_model->flag('deleted', 1, array('id' => $id)) ;
		if( ! $silent )
		{
			$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'user_group deleted successfully' : 'Unable to delete user_group'), array('_id' => $id)) ;
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

		$user_group = @$this->fields['cbList'] ;
		if( @count($user_group) < 1 )
		{
			$this->statusResponse('FAIL', 'There are no user_group') ;
			return false ;
		}

		//Do bulk action
		$msge = '' ;
		$stat = array('action-s' => 0, 'action-f' => 0) ;
		switch( $action )
		{
			case 'delete' :
				$msgs = '%s user_group(s) deleted.' ;
				$msge = '%f user_group(s) not deleted.' ;
				foreach( $user_group as $v )
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