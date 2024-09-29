<?php
class Telecast extends Controller
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
		$this->loadModel('telecast_model') ;
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
			$cond = " AND (s.name LIKE '%$filter%' OR w.name LIKE '%$filter%' OR b.name LIKE '%$filter%')" ;
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
		
		//Branch only data..
		$branchId = branchEmployee() ;
		if( $branchId )
		{
			$cond .= " AND b.id='$branchId' " ; 
		}

		$sql = "SELECT t.id, c.title, c.approved, w.id as window_id, t.from_dt, t.to_dt, c.type as content_type,  w.name as window, s.name as screen, b.name as branch FROM telecast t
				LEFT JOIN window w ON w.id = t.window_id
				LEFT JOIN screen s ON s.id = w.screen_id
				LEFT JOIN branch b ON b.id = s.branch_id
				INNER JOIN content c ON c.id = t.content_id
				WHERE 1 $cond $sort" ;
		
		$sqlcnt ="SELECT COUNT(*) as cnt FROM telecast t
				LEFT JOIN window w ON w.id = t.window_id
				LEFT JOIN screen s ON s.id = w.screen_id
				LEFT JOIN branch b ON b.id = s.branch_id
				INNER JOIN content c ON c.id = t.content_id
				WHERE 1 $cond" ;

		$url = siteUrl('telecast/listtable/') ;

        $this->vars['pager_url'] = $url ;
		if( $this->ifCsvExport() )
		{
			$order = array(
				'Telecast' => 'telecast',
				'Screen'=>'screen',
			) ;

			$this->exportCsv($sql, $order) ;
			return;
		}
		
		$result = $this->pagination->pager($sql, $sqlcnt, $url, 'idListArea' . get_class($this), $page) ;
		//render page with default template file.
		$this->vars['timeline'] = $this->getModel('telecast_model')->getTimeline(Date('Y-m-d')) ;
		parent::defListtable($result) ;
	}
	function windowui($window, $dt = null)
	{
		if( ! $dt )
		{
			$dt = Date('Y-m-d') ;
		}
		$this->vars['timeline'] = $this->getModel('telecast_model')->getTimeline($dt, $window) ;
		$this->vars['window_id'] = $window ;
		$this->vars['sel_date'] = $dt ;
		$this->loadView('telecast_ui.php') ;
	}
	function screenui($screen, $dt = null)
	{
		if( ! $dt )
		{
			$dt = Date('Y-m-d') ;
		}
		$this->vars['timeline'] = $this->getModel('telecast_model')->getTimeline($dt, null, $screen) ;
		$this->vars['screen_id'] = $screen ;
		$this->vars['sel_date'] = $dt ;
		$this->loadView('telecast_ui.php') ;
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
		$errors['eContent'] = '' ;
		$errors['eWindow'] = '' ;
		$errors['eFrom'] = '' ;
		
		//{ db check conditions
		if( $mode == 'edit' )
		{
			$editId = $this->getArg('editId') ;
			if( @$this->fields['txtFrom'] && @$this->fields['txtTo'] && @$this->fields['selWindow'] )
			{
				if( validateOverlap($this->fields['txtFrom'], $this->fields['txtTo'], $this->fields['selWindow'], $editId) )
				{
					$errors['eFrom'] = 'Schedule overlap with another telecast' ;
				}
			}
		}
		else if( $mode == 'add' )
		{
			if( @$this->fields['txtFrom'] && @$this->fields['txtTo'] && @$this->fields['selWindow'] )
			{
				if( validateOverlap($this->fields['txtFrom'], $this->fields['txtTo'], $this->fields['selWindow']) )
				{
					$errors['eFrom'] = 'Schedule overlap with another telecast' ;
				}
			}
		}
		//}

		//basic test cases		
		if( ! @$this->fields['txtContent'] )
		{
			$errors['eContent'] = 'Content not specified' ;
		}
		if( ! @$this->fields['txtFrom'] )
		{
			$errors['eFrom'] = 'From time not specified' ;
		}
		if( ! @$this->fields['txtTo'] )
		{
			$errors['eTo'] = 'To time not specified' ;
		}
		//basic test cases		
		if( ! @$this->fields['selWindow'] )
		{
			$errors['eWindow'] = 'Window not specified' ;
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
		
		$telecast = array(
				'window_id' => sqlNullableKey($this->fields['selWindow']),
				'content_id' => sqlNullableKey($this->fields['txtContent']),
				'from_dt' => mysqlDateTime($this->fields['txtFrom']),
				'to_dt' => mysqlDateTime($this->fields['txtTo']),
		) ;

		if( $this->telecast_model->insert($telecast) )
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
	function add($content_id = null, $branch_id_in = null)
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
		$branchId = branchEmployee() ;
		$where = array() ;
		if( $branchId )
		{
			$where = array(
				'id' => $branchId
			) ;
		}
		$this->vars['branches'] = $this->getModel('branch_model')->getWhereBy( $where );
		$this->vars['groups'] = $this->getModel('branch_group_model')->get();
		$this->vars['mode'] = 'add' ;
		$this->vars['url'] = siteUrl('telecast/add') ;
		//load content and branch
		$this->vars['result']['branch_id'] = (($branchId) ? $branchId : $branch_id_in) ;
		$this->vars['result']['content_id'] = $content_id ;
		if( $this->vars['result']['branch_id'] )
		{
			$this->vars['screens']=$this->getModel('screen_model')->getWhereBy( array('deleted' =>0, 'branch_id' => $this->vars['result']['branch_id']));
		}
		
		if( $content_id )
		{
			$sqlc = "SELECT title FROM content WHERE id='$content_id'" ;
			$this->vars['result']['title'] = $this->db->scalarField($sqlc) ;
		}
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

		$telecast = array(
				'window_id' => sqlNullableKey($this->fields['selWindow']),
				'content_id' => sqlNullableKey($this->fields['txtContent']),
				'from_dt' => mysqlDateTime($this->fields['txtFrom']),
				'to_dt' => mysqlDateTime($this->fields['txtTo']),
		) ;
		
		if( $this->telecast_model->update($telecast, array('id' => $id)) )
		{	
			$this->db->commitTrans();
			//return status
			return new JStatus(true, 'telecast details updated successfully') ;
		}
        $this->db->rollbackTrans();
		return new JStatus(false, 'Unable to update telecast details') ;
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
					$this->view($id) ;	
				}
				$jstat->data['idContentAreaBig'] = ob_get_clean() ;
			}
			$this->statusResponse($jstat) ;
			return false ;
		}

		
		$result = $this->telecast_model->getDetails($id) ;
		$this->vars['mode'] = 'edit' ;
		$this->setArg('editId', $id) ;
		$this->vars['url'] = siteUrl('telecast/edit' . '/' . $id ) ;
		$this->vars['result'] = $result ;
		$this->vars['screens']=$this->getModel('screen_model')->getWhereBy( array('branch_id' => $result['branch_id']));
		$this->vars['windows']=$this->getModel('window_model')->getWhereBy( array('screen_id' => $result['screen_id']));

		$branchId = branchEmployee() ;
		$where = array() ;
		if( $branchId )
		{
			$where = array(
				'id' => $branchId
			) ;
		}
		$this->vars['branches']=$this->getModel('branch_model')->getWhereBy( $where );

		$this->loadView('telecast_add.php') ;
	}
	/**
	 * Display indivitual details
	 */
	function view($id)
	{
		$rec = $this->telecast_model->getDetails($id) ;
		$rec_content = $this->loadModel('content_model')->getDetails($rec['content_id']) ;
		$rec['lang_data'] = $rec_content['lang_data'] ;

		$this->loadView('telecast_view.php', array('result' => $rec)) ;
	}
	/**
	 * Mark a record as deleted.
	 * 
	 * @param int $id id to remove
	 * @return bool
	 */
	function delete($id, $silent = false )
	{
		$status = $this->telecast_model->delete(array('id' => $id)) ;
		if( ! $silent )
		{
			$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'telecast deleted successfully' : 'Unable to delete telecast'), array('_id' => $id)) ;
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

		$telecast = @$this->fields['cbList'] ;
		if( @count($telecast) < 1 )
		{
			$this->statusResponse('FAIL', 'There are no telecast') ;
			return false ;
		}

		//Do bulk action
		$msge = '' ;
		$stat = array('action-s' => 0, 'action-f' => 0) ;
		switch( $action )
		{
			case 'delete' :
				$msgs = '%s telecast(s) deleted.' ;
				$msge = '%f telecast(s) not deleted.' ;
				foreach( $telecast as $v )
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