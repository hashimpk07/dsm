<?php
class User extends Controller
{
	public function __construct()
	{

		$acls = array(
			'allow' => array(
						'*' => '*' ),
			'deny' => array(),
			'order' => 'DA' //Allow, Then Deny (Options are "DA" or "AD")
		);
		$this->acl($acls) ;

		parent::__construct();

		/* @var $this->user_model Users_model */
		$this->loadModel('user_model') ;
		$this->loadModel('privilege_group_model') ;
		$this->loadModel('privilege_group_entry_model') ;
		$this->loadModel('user_privilege_model') ;
	}
	/**
	 * Default function
	 */
	function index()
	{
		//request will be routed to display.
		parent::defIndex() ;
	}
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
			//$cond = " AND (e.name LIKE '%$filter%' OR  e.group_code LIKE '%$filter%' OR  e.otherdata LIKE '%$filter%' OR  e.username LIKE '%$filter%')" ;
			$cond = " AND (e.name LIKE '%$filter%' OR e.username LIKE '%$filter%' OR (
								CASE WHEN e.branch_id = 0 THEN
									'" . QC_STR_ALL_LOCATIONS . "' LIKE '%$filter%'
								ELSE 
									c.name LIKE '%$filter%'
								END) )" ;

		}
		
		$opgroup = $this->input->request('searchq-opgroup');
		if( $opgroup )
		{
			$cond .= " AND e.user_group_id='" . $opgroup . "' " ;
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
			$sort = ' ORDER BY e.id DESC ' ;
		}
		//}

		$cond .= ' AND e.id != 1 ' ;

		//Branch only data..
		$branchId = branchEmployee() ;
		if( $branchId )
		{
			$cond .= " AND e.branch_id='$branchId' " ; 
		}
		
		$sql = "SELECT e.user_group_id, e.id, e.name, e.phone, e.email, e.blocked, e.username, c.name as branch FROM  user e
		            LEFT JOIN branch c ON c.id=e.branch_id
						WHERE e.deleted='0' AND e.user_group_id != '" . QC_USR_SUPERADMIN .  "' $cond $sort " ;

		$sqlcnt = "SELECT COUNT(*) as cnt FROM  user e
					LEFT JOIN branch c ON c.id=e.branch_id
						WHERE e.deleted='0' AND e.user_group_id != '" . QC_USR_SUPERADMIN .  "' $cond $sort" ;
		$url = siteUrl('user/listtable/') ;

		$this->vars['pager_url'] = $url ;
         if( $this->ifCsvExport() )
        {
           $order = array(
               'name' => 'Name',
               'username' => 'User Name',
			    'branch_id' => 'All Data',
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
	 * @param bool $edit true for edit..
	 * @return array validation results..
	 */
	function validate($mode)
	{
		$errors = array() ;
		$errors['eName'] = '' ;
		$errors['eUsername'] = '' ;
		$errors['ePassword'] = '' ;
		$errors['eOldPassword'] = '' ;
		$errors['eConfirmPassword'] = '' ;			

		//{ db check conditions
		if( $mode == 'edit' || $mode == 'profile' )
		{
			$editId = $this->getArg('editId') ;



			$usrCond = '' ;
			$eCond = " ( email = '" . $this->fields['txtEmail'] . "' AND id != '$editId' ) " ;
			$phCond = " ( phone = '" . $this->fields['txtPhone'] . "' AND id != '$editId' )" ;
		}
		else if( $mode == 'add' )
		{
			$usrCond = " ( username = '" . $this->fields['txtUsername'] . "' AND deleted=0 )" ;
			$eCond = " ( email = '" . $this->fields['txtEmail'] . "' AND deleted = 0 ) " ;
			$phCond = " ( phone = '" . $this->fields['txtPhone'] . "' AND deleted = 0 )" ;
		}
		//}
		//basic test cases		
		if( ! @$this->fields['txtName'] )
		{
			$errors['eName'] = 'Name not specified' ;
		}
		if( $mode == 'add' )
		{
			if( ! @$this->fields['txtUsername'] )
			{
				$errors['eUsername'] = 'Username not specified' ;
			}
			$eQuery = "SELECT COUNT(*) AS total FROM user e WHERE $usrCond" ;
			if( $this->db->scalarField($eQuery) )
			{
				$errors['eUsername'] = 'Username address already exists' ;
			}
			
		}
		if( @$this->fields['txtEmail'] )
		{
			$eQuery = "SELECT COUNT(*) AS total FROM user c WHERE $eCond" ;
			if( $this->db->scalarField($eQuery) )
			{
				$errors['eEmail'] = 'Email address already exists' ;
			}
		}
		if( @$this->fields['txtPhone'] )
		{
			$emailquery = "SELECT COUNT(*) AS total FROM user c WHERE $phCond" ;
			if( $this->db->scalarField($emailquery) )
			{
				$errors['ePhone'] = 'Phone number already exists' ;
			}
		}
		//db test cases
	/*	if( @$this->fields['txtEmail1'] )
		{
			$eQuery = "SELECT COUNT(*) AS total FROM user e WHERE $eCond1" ;
			if( $this->db->scalarField($eQuery) )
			{
				$errors['eEmail1'] = 'Email address already exists' ;
			}
			if( $this->fields['txtEmail1'] == $this->fields['txtEmail2'] )
			{
				$errors['eEmail2'] = 'Please specify a different email address' ;
			}
			
		}*/
		if( $mode == 'profile' )
		{
			if( @$this->fields['txtOldPassword'] )
			{
				$password = md5($this->fields['txtOldPassword']) ;
				$eQuery = "SELECT COUNT(*) AS total FROM user e WHERE e.id = '$editId' AND e.password='$password'" ;
				if( ! $this->db->scalarField($eQuery) )
				{
					$errors['eOldPassword'] = 'Incorrect old password' ;
				}
			}
			if( ! @$this->fields['txtPassword'] ) 
			{
				if( @$this->fields['txtConfirmPassword'] )
				{
					$errors['ePassword'] = 'Password not specified' ;
				}
			}
		}

		/*if( (! $this->fields['txtPhone1']) && (! $this->fields['txtEmail1']) && (! $this->fields['txtPhone2']) && (! $this->fields['txtEmail2']))
		{
			$errors['eEmail2'] = 'Eithor phone or email must be present.' ;
		}
		if( @$this->fields['txtPhone1'] )
		{
			$emailquery = "SELECT COUNT(*) AS total FROM user e WHERE $phCond1" ;

			if( $this->db->scalarField($emailquery) )
			{
				$errors['ePhone1'] = 'Phone number already exists' ;
			}
			if( $this->fields['txtPhone1'] == $this->fields['txtPhone2'] )
			{
				$errors['ePhone2'] = 'Please specify a different phone number' ;
			}
		}
		if( @$this->fields['txtEmail2'] )
		{
			$eQuery = "SELECT COUNT(*) AS total FROM user e WHERE $eCond2" ;
			if( $this->db->scalarField($eQuery) )
			{
				$errors['eEmail2'] = 'Email address already exists' ;
			}
		}
		if( @$this->fields['txtPhone2'] )
		{
			$emailquery = "SELECT COUNT(*) AS total FROM user e WHERE $phCond2" ;
			if( $this->db->scalarField($emailquery) )
			{
				$errors['ePhone2'] = 'Phone number already exists' ;
			}
		}
		*/
		if( @$this->fields['txtPassword'] )
		{
			if( strlen(@$this->fields['txtPassword']) < 6 )
			{
				$errors['ePassword'] = 'Password must be atleast 6 letters' ;
			}
		}
		if( @$this->fields['txtPassword'] != $this->fields['txtConfirmPassword'] )
		{
			$errors['eConfirmPassword'] = 'Password does not match' ;
		}
		
		if( $mode == 'profile' )
		{
			$errors2 = array() ;
			foreach( $errors as $k => $v )
			{
				$errors2['p'. $k] = $v ;
			}
			$errors = $errors2 ;
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
			
		$user = array(
			'name' => $this->fields['txtName'],
			'username' => $this->fields['txtUsername'],
			'phone' => $this->fields['txtPhone'],
			'email' => $this->fields['txtEmail'],
			'designation' => $this->fields['txtDesignation'],
			'remarks' => $this->fields['txtRemarks'],
			'user_group_id' => $this->fields['txtUserGroup'],
		) ;
		
		if( $this->fields['txtUserGroup'] == QC_USR_BRANCH_OP )
		{
			$user['branch_id'] = @$this->fields['selBranch'] ;
		}
		
		if( $this->fields['txtPassword'] )
		{
			$user['password'] = md5($this->fields['txtPassword']) ;
		}
		if( $this->user_model->insert($user) )
		{			
			$insId = $this->db->getLastInsertId() ;
			//add privileges
			$privGroup = @$this->fields['selGroup'] ;
			if( $privGroup )
			{
				$privs = $this->privilege_group_entry_model->getWhereBy(array('group_id' => $privGroup)) ;
				foreach( $privs as $one )
				{
					$entry = array(
						'user_id' => $insId,
						'privilege_id' => $one['privilege_id'],
					) ;
					$this->user_privilege_model->insert($entry) ;
				}
			}
			//}
			$this->db->commitTrans() ;

			return new JStatus(true, 'Successfully saved', array('__id' => $insId)) ;
		}
		$this->db->rollbackTrans() ;
		return new JStatus(false, 'Unable to save') ;
	}
	/**
	 * Shows add form
	 */
	function add($group)
	{
		if( $this->input->post('btnSubmit') || $this->input->post('btnSubmitContinue') )
		{
			$jstat = $this->onAdd() ;
			
			if( $jstat->status )
			{
				if( $this->input->post('btnSubmit') )
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
				else if( $this->input->post('btnSubmitContinue') )
				{
					$id = $jstat->data['__id'] ;
					$jstat->data['__script'] = "actionEdit( '" . siteUrl('user/privilege/') . "$id', {}, 'idContentAreaBig');" ;
				}
			}
			$this->statusResponse($jstat) ;
			return ;
		}
		
		$dynrows = array(
			array('name' => 'a', 'id' => 324),
			array('name' => 'bss', 'id' => 3),
			array('name' => 'rf', 'id' => 5),
		);
	//	$this->vars['countries'] = $this->getModel('countries_model')->get() ;
		$this->vars['mode'] = 'add' ;
		$this->vars['dynObj'] = new DynamicTable(get_class($this)) ;
		$this->vars['dynrows'] = $dynrows ;
		$this->vars['group'] = $group ;
		$where = array() ;
		if( $this->session->get('usr_grp_code') == QC_USR_BRANCH_OP )
		{
			$where = array('user_group_id' => QC_USR_BRANCH_OP ) ;
		}

		$this->vars['prilegegroups'] = $this->privilege_group_model->getWhereBy($where) ;
		$this->vars['url'] = siteUrl('user/add/' . $group) ;
		
		$branchId = branchEmployee() ;
		$where = array() ;
		if( $branchId )
		{
			$where = array(
				'id' => $branchId
			) ;
		}
		$this->vars['branches']=$this->getModel('branch_model')->getWhereBy( $where );
		
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
		$data = $this->user_model->getDetails($id) ;
		
		

		$user = array(
			'name' => $this->fields['txtName'],
			'username' => $this->fields['txtUsername'],
			'phone' => $this->fields['txtPhone'],
			'email' => $this->fields['txtEmail'],
			'designation' => $this->fields['txtDesignation'],
			'remarks' => $this->fields['txtRemarks'],
		) ;
		
		if( $this->fields['txtUserGroup'] == QC_USR_BRANCH_OP )
		{
			$user['branch_id'] = @$this->fields['selBranch'] ;
		}

		if( $this->fields['txtPassword'] )
		{
			$user['password'] = md5($this->fields['txtPassword']) ;
		}

		if( $this->user_model->update($user, array('id' => $id)) )
		{	
			//add privileges
			//delte existing priv set
			$dela = array(
				'user_id' => $id,
			) ;
			$this->user_privilege_model->delete($dela) ;

			$privGroup = @$this->fields['selGroup'] ;
			if( $privGroup )
			{
				$privs = $this->privilege_group_entry_model->getWhereBy(array('group_id' => $privGroup)) ;
				foreach( $privs as $one )
				{
					$entry = array(
						'user_id' => $id,
						'privilege_id' => $one['privilege_id'],
					) ;
					$this->user_privilege_model->insert($entry) ;
				}
			}
			//}
			$this->db->commitTrans();
			//return status
			return new JStatus(true, 'User details updated successfully') ;
		}

        $this->db->rollbackTrans();
		return new JStatus(false, 'Unable to update user details') ;
	}
	/**
	 * Edit id
	 * 
	 * @param int $id
	 */
	function edit($id)
	{
		if( $this->input->post('btnSubmit') || $this->input->post('btnSubmitContinue') )
		{
			$jstat = $this->onEdit($id) ;

			if( $jstat->status )
			{
				if( $this->input->post('btnSubmit') )
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
				else if( $this->input->post('btnSubmitContinue') )
				{
					$jstat->data['__script'] = "actionEdit( '" . siteUrl('user/privilege/') . "$id', {}, 'idContentAreaBig');" ;
				}
			}
			$this->statusResponse($jstat) ;
			return false ;
		}

		$result = $this->user_model->getDetails($id) ;
		$this->setArg('editId', $id) ;
		$this->vars['mode'] = 'edit' ;
		$this->vars['editId'] = $id ;
		$this->vars['url'] = siteUrl('user/edit' . '/' . $id ) ;
		$this->vars['result'] = $result ;
	//	$this->vars['countries'] = $this->getModel('countries_model')->get() ;	
		$this->vars['group'] = $result['user_group_id'];
		$this->vars['prilegegroups'] = $this->privilege_group_model->get() ;
		
		$branchId = branchEmployee() ;
		$where = array() ;
		if( $branchId )
		{
			$where = array(
				'id' => $branchId
			) ;
		}
		$this->vars['branches']=$this->getModel('branch_model')->getWhereBy( $where );
		
		$this->loadView('user_add.php') ;
	}
	
	/**
	 * On edit submit
	 */
	private function onProfile($id)
	{
		$errors = $this->validate('profile') ;
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
		$data = $this->user_model->getDetails($id) ;

		$user = array(
			'name' => $this->fields['txtName'],
			'phone' => $this->fields['txtPhone'],
			'email' => $this->fields['txtEmail'],
		) ;

		if( $this->fields['txtPassword'] )
		{
			$user['password'] = md5($this->fields['txtPassword']) ;
		}

		if( $this->user_model->update($user, array('id' => $id)) )
		{	
			$this->db->commitTrans();
			//return status
			$dtret = array() ;
			$dtret['ptxtOldPassword'] = '' ;
			$dtret['ptxtPassword'] = '' ;
			$dtret['ptxtConfirmPassword'] = '' ;
			$dtret = array_merge($errors, $dtret) ;
			return new JStatus(true, 'Profile updated successfully', $dtret) ;
		}

        $this->db->rollbackTrans();
		return new JStatus(false, 'Unable to update profile') ;
	}
	/**
	 * Edit id
	 * 
	 * @param int $id
	 */
	function profile($id)
	{
		if( $this->input->post('btnSubmit') )
		{
			$jstat = $this->onProfile($id) ;

			$this->statusResponse($jstat) ;
			return false ;
		}

		$this->setArg('editId', $id) ;
		$this->vars['mode'] = 'edit' ;
		$this->vars['editId'] = $id ;
		$this->vars['url'] = siteUrl('user/profile' . '/' . $id ) ;
		$this->vars['result'] = $this->user_model->getDetails($id) ;
		//$this->vars['countries'] = $this->getModel('countries_model')->get() ;			
		$this->loadView('user_profile.php') ;
	}
	
	/**
	 * Display indivitual details
	 */
	function view($id)
	{
		$rec = $this->user_model->getDetails($id) ;
		$this->loadView('user_view.php', array('result' => $rec)) ;
	}
	/**
	 * Mark a record as deleted.
	 * 
	 * @param int $id id to remove
	 * @return bool
	 */
	function delete($id, $silent = false )
	{
		$status = $this->user_model->flag('deleted', 1, array('id' => $id)) ;
		if( ! $silent )
		{
			$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'User deleted successfully' : 'Unable to delete user'), array('_id' => $id)) ;
		}
		return $status;
	}
	/**
	 * Mark a record as deleted.
	 * 
	 * @param int $id id to remove
	 * @return bool
	 */
	function block($id, $block, $silent = false )
	{
		$status = $this->user_model->flag('blocked', $block, array('id' => $id)) ;
		
		if( $block )
		{
			if( ! $silent )
			{
				$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'User blocked successfully' : 'Unable to block'), array('_id' => $id)) ;
			}
		}
		else
		{
			if( ! $silent )
			{
				$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'User unblocked successfully' : 'Unable to unblock'), array('_id' => $id)) ;
			}
		}
		
		
		return $status;
	}
	function privilegeDelete($tplid, $silent=false)
	{
		$this->loadModel('user_templates_model') ;
		$status = $this->user_templates_model->flag('et_deleted', 1, array('et_id' => $tplid)) ;
		if( ! $silent )
		{
			ob_start() ;
			$vars['PRIV_LEVEL'] = 'E' ;
			$vars['templates'] = $this->user_templates_model->getWhere(array('et_deleted' => 0)) ;
			$this->loadView('privilege_templates', $vars) ;
			$tplData = ob_get_clean() ;
			
			$data = array(
						'__id' => $tplid,
						'idPrivilegeTemplateArea' => $tplData
					) ;
			$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'Template deleted successfully' : 'Unable to delete template'), $data ) ;
		}
		return $status;
	}
	function privilegeTemplates($tplid = 0, $empId=0)
	{
		$vars['PRIV_TPL'] = $tplid ;
		$vars['PRIV_LEVEL'] = 'E' ;
		$vars['PRIV_USER'] = $empId ;

		return $this->loadView('privilege.php', $vars);
	}
	function privilege($id)
	{
		$this->loadModel('user_templates_model') ;
		
		$vars['PRIV_TPL'] = 0 ;
		$vars['PRIV_LEVEL'] = 'E' ;
		$vars['PRIV_USER'] = $id ;

		if( $this->input->ispost('btnSubmit') )
		{
			//insert privileges..
			$qry= "DELETE FROM user_privileges WHERE ep_emp_id='$id'";
			$this->db->execute($qry) ;
			if( is_array(@$this->fields['cbPrivileges']) )
			{
				foreach( $this->fields['cbPrivileges'] as $v )
				{
					$ep_emp_id=sqlNullableKeyString($id);
					$ep_priv_id=sqlNullableKeyString($v);
					$sql = "INSERT INTO user_privileges(ep_emp_id, ep_priv_id) VALUES($ep_emp_id, $ep_priv_id);" ;
					$this->db->execute($sql) ;
				}
			}
			if( ! $this->db->commitTrans() )
			{
				$this->statusResponse( 'FAIL', 'Privileges not saved successfully.', array('_id' => $id) ) ;
				//send status ok response			
				return false ;
			}
			$this->statusResponse( 'OK', 'Privileges saved successfully.', array('_id' => $id, 'idWorkArea' => '') ) ;
			return ;
		}
		else if( $this->input->ispost('btnupdate') )
		{
			$this->loadModel('user_template_details_model') ;

			$tplNameOrId = $this->fields['cbTemplates'] ;
			$tplId = urldecode($tplNameOrId) ;
			$dataet = array('et_name' => $tplId) ;

			if( ! intval($tplId) )
			{				
				if( $this->user_templates_model->insert($dataet) )
				{
					$tplId = $this->user_templates_model->db->getLastInsertId() ;
				}
			}
			if( $tplId )
			{
				//Delete all existing..
				$where = array('etd_et_id' => $tplId) ;
				$this->user_template_details_model->delete($where) ;
				//Insert all 
				foreach( $this->fields['cbPrivilege'] as $v )
				{
					$dataetd = array(
						'etd_et_id' => sqlNullableKey($tplId),
						'etd_ep_id' => sqlNullableKey($v),
					) ;
					$this->user_template_details_model->insert($dataetd) ;
				}
				
				ob_start() ;
				$vars['PRIV_TPL'] = $tplId ;
				$vars['templates'] = $this->user_templates_model->getWhere(array('et_deleted' => 0)) ;
				$this->loadView('privilege_templates', $vars) ;
				$tplData = ob_get_clean() ;

				$data = array(
							'__id' => $tplId,
							'idPrivilegeTemplateArea' => $tplData
						) ;
				return $this->statusResponse(new JStatus('OK', 'Template Updated successfully.', $data )) ;
			}
		}

		
		ob_start() ;
		$this->privilegeTemplates(0, $id) ;
		$vars['privilege_content'] = ob_get_clean() ;

		//get template list..
		$this->setArg('empId',$id) ;
		$vars['templates'] = $this->user_templates_model->getWhere(array('et_deleted' => 0)) ;
		return $this->loadView('user_privileges.php', $vars);
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
		$stat = array('action-s' => 0, 'action-f' => 0) ;
		switch( $action )
		{
			case 'delete' :
				$msgs = '%s user(s) deleted.' ;
				$msge = '%f user(s) not deleted.' ;
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
			case 'block' :
				$msgs = '%s user(s) blocked.' ;
				$msge = '%f user(s) not blocked.' ;
				foreach( $items as $v )
				{
					if( $this->block($v, 1, true) )
					{
						$stat['action-s'] ++ ;
					}
					else
					{
						$stat['action-f'] ++ ;
					}
				}
				break ;
			case 'unblock' :
				$msgs = '%s user(s) unblocked.' ;
				$msge = '%f user(s) not unblocked.' ;
				foreach( $items as $v )
				{
					if( $this->block($v, 0, true) )
					{
						$stat['action-s'] ++ ;
					}
					else
					{
						$stat['action-f'] ++ ;
					}
				}
				break ;
			case 'message' :
				$dataset['readonly'] = 1;
				$dataset['usertype'] = QC_USR_EMPLOYEE;
				$dataset['userlist'] = $items;
				$str = json_encode($dataset) ;
				$data = "getData('" . siteUrl('alert/add') . "'," . $str . ", 'idContentAreaBig' );" ;
				return $this->statusResponse('OK', '', array('__script' => $data)) ;
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