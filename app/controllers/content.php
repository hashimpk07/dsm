<?php
class Content extends Controller
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
		$this->loadModel('content_model') ;
		$this->loadModel('content_lang_model') ;
		$this->loadModel('language_model') ;
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
			$cond = " AND (c.title LIKE '%$filter%' OR u.username LIKE '%$filter%' )" ;
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
			$sort = ' ORDER BY id DESC' ;
		}
		//}

		//Branch only data..
		$branchId = branchEmployee() ;
		if( $branchId )
		{
			$cond .= " AND (c.branch_id='$branchId' OR c.classification='G') " ;
		}

		$sql="SELECT c.*, u.username FROM content c
				 LEFT JOIN user u ON c.approved_by = u.id WHERE c.deleted='0' $cond $sort";

		$sqlcnt = "SELECT COUNT(*) as total FROM content c
					LEFT JOIN user u ON c.approved_by = u.id WHERE c.deleted='0' $cond" ;

		$url = siteUrl('content/listtable/') ;

		$this->vars['pager_url'] = $url ;

		if( $this->ifCsvExport() )
		{
			$order = array(
				'name' => 'Name',
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
		$errors['eTitle'] = '' ;
		$errors['eFrom'] = '' ;
		$errors['eTo'] = '' ;

		//basic test cases
		if( ! @$this->fields['txtTitle'] )
		{
			$errors['eTitle'] = 'Title not specified' ;
		}

		if( @$this->fields['hidClassification'] != 'G' )
		{
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
				if( @$this->fields['txtFrom'] && @$this->fields['txtTo'] && @$this->fields['selWindow'] && @$this->fields['txtContent'] )
				{
					if( validateOverlap($this->fields['txtFrom'], $this->fields['txtTo'], $this->fields['selWindow']) )
					{
						$errors['eFrom'] = 'Schedule overlap with another telecast' ;
					}
				}
			}
			//}

			if( @$this->fields['txtFrom'] && @$this->fields['txtTo'] )
			{
				$tfrom = Date('1/1/1970 H:i:s' , strtotime($this->fields['txtFrom'])) ;
				$tto   = Date('1/1/1970 H:i:s' , strtotime($this->fields['txtTo'])) ;
				if( $tfrom >= $tto)
				{
					$errors['eTo'] = 'To time must be after From time' ;
				}
			}
			else
			{
				if( @$this->fields['selWindow'] )
				{
					if( ! $this->fields['txtFrom'] )
					{
						$errors['eFrom'] = 'Invalid From time' ;
					}
					if( ! $this->fields['txtTo'] )
					{
						$errors['eTo'] = 'Invalid To time' ;
					}
				}
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

		$type = $this->fields['selType'] ;
		$fieldName = '' ;
		if( $type == QC_CONTENT_TYPE_I )
		{
			$fieldName = 'filDataImage' ;
		}
		if( $type == QC_CONTENT_TYPE_V )
		{
			$fieldName = 'filDataVideo' ;
		}
		$data = '' ;
		if( $fieldName )
		{
			//Upload file
			if (!empty($_FILES))
			{
				//get uploaded file extension
				$ext = getFileExtension($_FILES[$fieldName]['name']) ;
				$this->loadLibrary('upload');
				$config = array(
					'upload_path' => fileUrl('app/data/'),
					'file_name' => 'file_' . '_' . getUniqId() . '.' . $ext,
				);


				$uploaded = $this->upload->doUpload($fieldName, $config);
				if ($uploaded)
				{
					$data = $config['file_name'] ;
				}
			}
		}
		else
		{
			if( $type == QC_CONTENT_TYPE_T )
			{
				$data = $this->fields['txtDataText'] ;
			}
			else if( $type == QC_CONTENT_TYPE_H )
			{
				$data = $this->fields['txtDataHtml'] ;
			}
			else if( $type == QC_CONTENT_TYPE_S )
			{
				$data = $this->fields['txtDataScrollingText'] ;
			}
			else if( $type == QC_CONTENT_TYPE_IU )
			{
				$data = $this->fields['txtDataImageUrl'] ;
			}
			else if( $type == QC_CONTENT_TYPE_VU )
			{
				$data = $this->fields['txtDataVideoUrl'] ;
			}
		}


		$this->db->beginTrans() ;

		$content = array(
				'title' => $this->fields['txtTitle'],
				'classification' => $this->fields['hidClassification'],
			    'type' => $this->fields['selType'],
			    'future' => ((@$this->fields['cbFuture'])? 1 : 0),
			    'dt' => Date('Y-m-d H:i:s')
			) ;

		if( @$this->fields['selBranch'] )
		{
			$content['branch_id'] = $this->fields['selBranch'] ;
		}

		$languages = $this->language_model->getWhereBy(array('deleted' => '0')) ;

		if( $this->content_model->insert($content) )
		{
			$insId = $this->db->getLastInsertId() ;
			//insert language data..
			//default language is id 1
			if( $type == QC_CONTENT_TYPE_T || $type == QC_CONTENT_TYPE_S )
			{
				foreach( $languages as $lang )
				{
					$realData = '' ;
					if( $type == QC_CONTENT_TYPE_T )
					{
						$realData = $this->fields['txtDataText'][$lang['id']] ;
					}
					else if( $type == QC_CONTENT_TYPE_S )
					{
						$realData = $this->fields['txtDataScrollingText'][$lang['id']] ;
					}
					$dataA = array(
						'content_id' => $insId,
						'lang_id' => $lang['id'],
						'data' => $realData,
					) ;

					$this->content_lang_model->insert($dataA) ;
				}
			}
			else
			{
				$realData = $data ;

				$dataA = array(
					'content_id' => $insId,
					'lang_id' => QC_LANGUAGE_DEFAULT,
					'data' => $realData,
				) ;

				$this->content_lang_model->insert($dataA) ;
			}

			//Aplly content to a screen window {
			if( @$this->fields['selWindow'] )
			{
				$apply = array(
					'window_id' => $this->fields['selWindow'],
					'from_dt' => mysqlDateTime($this->fields['txtFrom']),
					'to_dt' => mysqlDateTime($this->fields['txtTo']),
					'content_id' => $insId,
					) ;
				$this->getModel('telecast_model')->insert($apply) ;
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
	function add($classification='R')
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

		//$this->vars['parents'] = $this->content_model->getParents();
		$this->vars['mode'] = 'add' ;
		$this->vars['types'] = $this->getModel('content_type_model')->all();
		$this->vars['languages']=$this->getModel('language_model')->getWhereBy(array('deleted' => 0));
		$this->vars['url'] = siteUrl('content/add') ;
		$this->vars['classification'] = $classification ;

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
	function global_message()
	{
		$this->add('G') ;
	}
	function branch_message()
	{
		$this->add('B') ;
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

		$type = $this->fields['selType'] ;
		$fieldName = '' ;
		if( $type == QC_CONTENT_TYPE_I )
		{
			$fieldName = 'filDataImage' ;
		}
		if( $type == QC_CONTENT_TYPE_V )
		{
			$fieldName = 'filDataVideo' ;
		}
		$data = '' ;
		if( $fieldName )
		{
			//Upload file
			if (!empty($_FILES))
			{
				//get uploaded file extension
				$ext = getFileExtension($_FILES['txtFile']) ;
				$this->loadLibrary('upload');
				$config = array(
					'upload_path' => fileUrl('app/data/'),
					'file_name' => 'file_' . '_' . getUniqId() . '.' . $ext,
				);
			}

			$uploaded = $this->upload->doUpload('txtFile', $config);
			$data = '' ;
			if ($uploaded)
			{
				$data = $config['file_name'] ;
			}
		}
		else
		{
			if( $type == QC_CONTENT_TYPE_T )
			{
				$data = $this->fields['txtDataText'] ;
			}
			else if( $type == QC_CONTENT_TYPE_H )
			{
				$data = $this->fields['txtDataHtml'] ;
			}
			else if( $type == QC_CONTENT_TYPE_S )
			{
				$data = $this->fields['txtDataScrollingText'] ;
			}
			else if( $type == QC_CONTENT_TYPE_IU )
			{
				$data = $this->fields['txtDataImageUrl'] ;
			}
			else if( $type == QC_CONTENT_TYPE_VU )
			{
				$data = $this->fields['txtDataVideoUrl'] ;
			}
		}

        $this->db->beginTrans() ;

		$content = array(
				'classification' => $this->fields['hidClassification'],
			    'title' => $this->fields['txtTitle'],
			    'type' => $this->fields['selType'],
			    'future' => ((@$this->fields['cbFuture'])? 1 : 0),
			    'dt' => Date('Y-m-d H:i:s')
			) ;

		if( @$this->fields['selBranch'] )
		{
			$content['branch_id'] = $this->fields['selBranch'] ;
		}

		$languages = $this->language_model->getWhereBy(array('deleted' => 0)) ;

		if( $this->content_model->update($content, array('id' => $id)) )
		{
			//default language is id 1
			if( $type == QC_CONTENT_TYPE_T || $type == QC_CONTENT_TYPE_S )
			{
				foreach( $languages as $lang )
				{
					$realData = '' ;
					if( $type == QC_CONTENT_TYPE_T )
					{
						$realData = $this->fields['txtDataText'][$lang['id']] ;
					}
					else if( $type == QC_CONTENT_TYPE_S )
					{
						$realData = $this->fields['txtDataScrollingText'][$lang['id']] ;
					}
					$dataA = array(
						'data' => $realData,
					) ;

					$this->content_lang_model->update($dataA, array('lang_id' => $lang['id'], 'content_id' => $id)) ;
				}
			}
			else
			{
				$realData = $data ;

				$dataA = array(
					'data' => $realData,
				) ;

				$this->content_lang_model->update($dataA, array('lang_id' => QC_LANGUAGE_DEFAULT, 'content_id' => $id)) ;
			}

			$this->db->commitTrans() ;

			return new JStatus(true, 'Successfully saved', array('__id' => $id)) ;
		}
		$this->db->rollbackTrans() ;

		return new JStatus(false, 'Unable to update content details') ;
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

		$result = $this->content_model->getWhereOne(array('id' => $id)) ;
		$result['lang_data'] = $this->content_lang_model->getKVByContentId($id) ;

		$this->vars['mode'] = 'edit' ;
		$this->setArg('editId', $id) ;
		$this->vars['types'] = $this->getModel('content_type_model')->all();
		$this->vars['url'] = siteUrl('content/edit' . '/' . $id ) ;
		$this->vars['result'] = $result ;
		$this->vars['screens'] = $this->getModel('screen_model')->get();
		$this->vars['classification'] = $result['classification'] ;
		$this->vars['languages']=$this->getModel('language_model')->getWhereBy(array('deleted' => 0));


		$branchId = branchEmployee() ;
		$where = array() ;
		if( $branchId )
		{
			$where = array(
				'id' => $branchId
			) ;
		}
		$this->vars['branches']=$this->getModel('branch_model')->getWhereBy( $where );

		$this->loadView('content_add.php') ;
	}
	/**
	 * Display indivitual details
	 */
	function view($id)
	{
		$rec = $this->content_model->getDetails($id) ;
        return $this->loadView('content_view.php', array('result' => $rec)) ;
	}
	/**
	 * Mark a record as deleted.
	 *
	 * @param int $id id to remove
	 * @return bool
	 */
	function delete($id, $silent = false )
	{
		$status = $this->content_model->flag('deleted', 1, array('id' => $id)) ;
		if( ! $silent )
		{
			$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'content deleted successfully' : 'Unable to delete content'), array('_id' => $id)) ;
		}
		return $status;
	}
	/**
	 * Mark a record as deleted.
	 *
	 * @param int $id id to remove
	 * @return bool
	 */
	function approve($id, $approve, $silent = false )
	{
		$data = array(
			'approved_by' => $this->session->get('usr_id'),
			'approved_dt' => Date('Y-m-d H:i:s'),
			'approved' => $approve,
		) ;

		$status = $this->content_model->update($data, array('id' => $id)) ;

		if( $approve )
		{
			if( ! $silent )
			{
				$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'Content approved successfully' : 'Failed to approve'), array('_id' => $id)) ;
			}
		}
		else
		{
			if( ! $silent )
			{
				$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'Content refused successfully' : 'Failed to refuse'), array('_id' => $id)) ;
			}
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
				$msgs = '%s content deleted.' ;
				$msge = '%f content not deleted.' ;
				}
				else
				{
				$msgs = '%s content deleted.' ;
				$msge = '%f content not deleted.' ;
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
	function  upload()
	{
		//Upload file
		if (!empty($_FILES))
		{
			//get uploaded file extension
			$ext = getFileExtension($_FILES['filWindowBackground']['name']) ;
			$this->loadLibrary('upload');
			$config = array(
				'upload_path' => fileUrl('app/data/'),
				'file_name' => 'file_' . '_' . getUniqId() . '.' . $ext,
			);


			$uploaded = $this->upload->doUpload('filWindowBackground', $config);
			if ($uploaded)
			{
				echo baseUrl('app/data/' . $config['file_name']) ;
			}
		}
	}
}
?>