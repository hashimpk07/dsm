<?php
class Location extends Controller
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
		$this->loadModel('location_model') ;
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
			$cond = " AND (c.name LIKE '%$filter%')" ;
		}
		//{sort
		$sort = '' ;
		$sortTreeControlSymbol = '' ;
		if( @$this->input->request['searchq-col'] || @$this->input->request['hid-searchq-col'] )
		{
			$sort = ' ORDER BY ' . ( ($this->input->request['searchq-col']) ? $this->input->request['searchq-col'] : @$this->input->request['hid-searchq-col'] ) ;
			if( (@$this->input->request['searchq-sort'] == 'desc') || (@$this->input->request['hid-searchq-sort'] == 'desc') )
			{
				$sort .= ' DESC ' ;
				$sortTreeControlSymbol = chr(0xFF) ; //Last character in UTF8
			}
			else
			{
				$sort .= ' ASC ' ;
				$sortTreeControlSymbol = chr(0x00) ; //First character in UTF8
			}
		}
		if( ! $sort )
		{
			$sortTreeControlSymbol = chr(0x00) ; //First character in UTF8
			$sort = ' ORDER BY name ASC' ;
		}
		//}

				/*$sql    = "SELECT c.cat_name, c2.cat_name AS parent_name, c.cat_id, c.cat_parent_id, 
					if( isnull(c2.cat_name), concat(c.cat_name, '$sortTreeControlSymbol'), concat(c2.cat_name, '$sortTreeControlSymbol', c.cat_name)) as cat_grp_name,
					if( isnull( concat( c2.cat_name, c.cat_name ) ) , c.cat_id, concat( c2.cat_id, '-', c.cat_id ) ) AS cat_grp FROM location c 
					LEFT JOIN location c2 ON c2.cat_id=c.cat_parent_id
					WHERE c.cat_deleted='0' $cond $sort" ;*/
				$sql="SELECT c.name, c.id FROM location c  WHERE c.deleted='0' $cond $sort";		
							
		$sqlcnt = "SELECT COUNT(*) as total FROM location c
					WHERE c.deleted='0' $cond" ;
		$url = siteUrl('location/listtable/') ;

		$this->vars['pager_url'] = $url ;

			if( $this->ifCsvExport() )
											{
												$order = array(
												'name' => 'Name(Tree)',
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
			$codeCond = " ( name = '" . $this->fields['txtName'] . "' AND id != '$editId' AND deleted = 0 )" ;
		}
		else if( $mode == 'add' )
		{
			$codeCond = " ( name = '" . $this->fields['txtName'] . "' AND deleted = 0 )" ;
		}
		//}

		//basic test cases		
		if(@$this->fields['txtName'] )
		{
			if( $this->location_model->isExists($codeCond))
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
			return new JStatus(false, 'Please fix validation errors', $errors) ;
			
		}

		$this->db->beginTrans() ;

		$location = array(
			    'name' => $this->fields['txtName'],
				'createby' =>$this->session->get('usr_id'),
				'updateby' =>$this->session->get('usr_id'),
				'createdt' => mysqlDateTime(Date('Y-m-d H:i:s')),
				'updatedt' => mysqlDateTime(Date('Y-m-d H:i:s')),
				) ;
		if ($this->location_model->insert($location))
		{
			$insId = $this->db->getLastInsertId();
				$junkdetails = array(
				array(
					'location_id' => $insId,
					'name' => 'Junk',
					'type' => QC_DEPT_TYPE_JUNK,
				),
				array(
					'location_id' => $insId,
					'name' => 'NewStock',
					'type' => QC_DEPT_TYPE_NEW,
				),
			);

			foreach($junkdetails as $datajunk){ 	   
		$this->getModel('department_model')->insert($datajunk);}
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
		
		//$this->vars['parents'] = $this->location_model->getParents();
		$this->vars['mode'] = 'add' ;
		$this->vars['url'] = siteUrl('location/add') ;
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
			return new JStatus(false, 'Please fix validation errors', $errors) ;
		}

        $this->db->beginTrans() ;
		//get current values 
		$location = array(
			    'name' => $this->fields['txtName'],
				'updatedt' => mysqlDateTime(Date('Y-m-d H:i:s')),
				'updateby' =>$this->session->get('usr_id')
				) ;

		if( $this->location_model->update($location, array('id' => $id)) )
		{	
			$this->db->commitTrans();
			//return status
			return new JStatus(true, 'Location details updated successfully') ;
		}
        $this->db->rollbackTrans();
		return new JStatus(false, 'Unable to update location details') ;
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
	    $this->vars['hasChild'] = $this->location_model->hasSubCategory($id); 
		$this->vars['url'] = siteUrl('location/edit' . '/' . $id ) ;
		$this->vars['result'] = $this->location_model->getDetails($id) ;
		$this->loadView('location_add.php') ;
	}
	/**
	 * Display indivitual details
	 */
	function view($id)
	{
		$rec = $this->location_model->getDetails($id) ;
        return $this->loadView('location_view.php', array('result' => $rec)) ;
	}
	/**
	 * Mark a record as deleted.
	 * 
	 * @param int $id id to remove
	 * @return bool
	 */
	function delete($id, $silent = false )
	{
		/*if( $this->getModel('location_model')->isExists(array('cat_parent_id' => $id, 'cat_deleted' => 0)) )
		{
			if( ! $silent )
			{
				$this->statusResponse( 'Fail', 'Unable to delete location, location has child', array('_id' => $id)) ;
			}
			return false ;
		}*/
	
		
		$sql = "SELECT id FROM department WHERE location_id=$id" ;

	   	$this->department_id = $this->db->scalarField($sql) ;
	     $this->department_id;
			if( $this->department_id )
		{
			
			$department_id = $this->department_id ;
			$sqld = "SELECT * FROM transfer WHERE from_department_id='$department_id' AND to_department_id='$department_id'" ;
			$departments = $this->db->fetchRowSet($sqld);
			
			//$QFA['location.departments'] = $departments ;
		}
		if( is_array(@$departments) )
		{
			foreach( $departments as $d )
			{
				$this->department_ids[] = $d['id'] ;
				
					if( $this->getModel('transfer_model')->isExists(array('from_department_id' => $department_ids,'to_department_id'=>$department_ids)) )
						{
						
						if( ! $silent )
							{
							$this->statusResponse( 'Fail', 'Unable to delete Location, Location exists in a Transfer', array('_id' => $id)) ;
							}
							return false ;
						}
				
			}
		}
		
		if($this->getModel('department_model')->isExists(array('location_id' => $id, 'deleted' => 0,'type'=>'D')) )
		{
			if( ! $silent )
			{
				$this->statusResponse( 'Fail', 'Unable to delete location, department exists', array('_id' => $id)) ;
			}
			return false ;
		}
		if($this->getModel('user_model')->isExists(array('location_id' => $id, 'deleted' => 0 )) )
		{
			if( ! $silent )
			{
				$this->statusResponse( 'Fail', 'Unable to delete location, employee connected to the location', array('_id' => $id)) ;
			}
			return false ;
		}
		$status = $this->location_model->flag('deleted', 1, array('id' => $id));
	
		//$status = $this->location_model->flag('deleted', 1, array('id' => $id,'type'=> 'D')) ;
		if( ! $silent )
		{
		   $this->getModel('department_model')->flag('deleted', 1, array('location_id' => $id,'type'=>'N'));
		   $this->getModel('department_model')->flag('deleted', 1, array('location_id' => $id,'type'=>'J'));
			   
		   $this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'Location deleted successfully' : 'Unable to delete Location'), array('_id' => $id)) ;
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
		
				if(@count($items)==1)
				{	
				$msgs = '%s location deleted.' ;
				$msge = '%f location not deleted.' ;
				}
				else 
				{
				$msgs = '%s location deleted.' ;
				$msge = '%f location not deleted.' ;
				}
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