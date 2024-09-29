<?php
class Canvas extends Controller
{
	public function __construct()	{

		$acls = array(
			'allow' => array(
						'*' => '*' ),
			'deny' => array(),
			'order' => 'AD', //Allow, Then Deny (Options are "DA" or "AD")
		);
		$this->acl($acls) ;

		parent::__construct();

		/* @var $this->brokers_model Brokers_model */
		$this->loadModel('screen_model') ;
		$this->loadModel('window_model') ;
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
	function design($id)
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
			$sort = ' ORDER BY b.id DESC ' ;
		}

		$sql = "SELECT b.id,b.name,s.name as screen FROM screen b
				LEFT JOIN screen s ON s.id = b.screen_id
				WHERE b.deleted='0' $cond $sort" ;
		
		$sqlcnt ="SELECT COUNT(*) as cnt FROM screen b
				LEFT JOIN screen s ON s.id = b.screen_id
				WHERE b.deleted='0' $cond" ;

		$url = siteUrl('canvas/add/') ;

        $this->vars['pager_url'] = $url ;
		if( $this->ifCsvExport() )
		{
			$order = array(
				'Branch' => 'screen',
				'Screen'=>'screen',
			) ;

			$this->exportCsv($sql, $order) ;
			return;
		}
		
		$screen_details = $this->screen_model->getWhereOne( array('id' => $id)) ;
		$wind_details = $this->screen_model->getWindowDetails($id) ;
		$fonts = $this->getModel('font_model')->get() ;
		
		//render page with default template file.
		$this->vars['windows'] = $wind_details ;
		$this->vars['screen'] = $screen_details ;
		$this->vars['fonts'] = $fonts ;
		$this->vars['url'] = $url ;
		$this->loadView('canvas.php') ;
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
		
		$name = $this->fields['txtScreenName'] ;
		
		//{ db check conditions
		$editId = $this->fields['editId'] ;
		if( $editId )
		{
			$eCond = " ( name = '" . $this->fields['txtScreenName'] . "' AND id != '$editId' AND deleted = 0 ) " ;
		}
		else if( $mode == 'add' )
		{
			$eCond = " ( name = '" . $this->fields['txtScreenName'] . "' AND deleted = 0 ) " ;
		}
		//}

		//basic test cases		
		if( ! @$this->fields['txtScreenName'] )
		{
			$errors['eName'] = 'Name not specified' ;
		}
		//db test cases
		if( @$this->fields['txtScreenName'] )
		{
			if( $this->screen_model->isExists($eCond) )
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
		
		$screen = array(
			    'name' => $this->fields['txtScreenName'],
			    'w' => $this->fields['txtScreenWidth'],
			    'h' => $this->fields['txtScreenHeight'],
			    'background' => $this->fields['txtScreenBackground'],
		) ;
		
		if( $this->screen_model->insert($screen) )
		{	
			$insId = $this->db->getLastInsertId() ;
			//update windows
			foreach( $this->fields['txtTop'] as $k => $something )
			{
				$window = array(
					'screen_id' => $insId,
					'name' => $this->fields['txtName'][$k],
					'x' => $this->fields['txtTop'][$k],
					'y' => $this->fields['txtTop'][$k],
					'w' => $this->fields['txtWidth'][$k],
					'h' => $this->fields['txtHeight'][$k],
					'font_weight' => $this->fields['txtBold'][$k],
					'font_style' => $this->fields['txtItalic'][$k],
					'text_decoration' => $this->fields['txtUnderline'][$k],
					'font_size' => $this->fields['txtFontSize'][$k],
					'background' => addslashes( toSingleQuote( urldecode( $this->fields['txtBackground'][$k] ) ) ),
					'text_color' => $this->fields['txtColor'][$k],
				) ;
				$this->window_model->insert($window) ;
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
		if( @$this->input->post('btnSubmit') || @$this->input->post('btnApply') )
		{
			$edit = $this->fields['editId'] ;
			
			if( $edit )
			{
				$jstat = $this->onEdit($edit) ;
			}
			else
			{
				$jstat = $this->onAdd() ;
			}
			
			if( $jstat->status )
			{
				if( $this->getArg('contentAreaClicked') != 'idPopupSubmit' )
				{
					if( isset($this->fields['btnSubmit']) )
					{
						$url = siteUrl('screen/page') ;
						$jstat->data['__script'] = "actionView('$url', {}, 'idContentAreaSmall'); " ;
					}
				}
			}
			$this->statusResponse($jstat) ;
			return ;
		}
		$this->vars['screens']=$this->getModel('screen_model')->get();

		$this->vars['mode'] = 'add' ;
		$this->vars['url'] = siteUrl('screen/add') ;
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

		$this->db->beginTrans() ;
		
		$screen = array(
			    'name' => $this->fields['txtScreenName'],
			    'w' => $this->fields['txtScreenWidth'],
			    'h' => $this->fields['txtScreenHeight'],
				'background' => $this->fields['txtScreenBackground'],
		) ;
		
		if( $this->screen_model->update($screen, array('id' => $id)) )
		{	
			//delete existing windows..
			$wids = array() ;
			foreach( $this->fields['txtTop'] as $k => $something )
			{
				$wids[] = $this->fields['txtId'][$k] ;
			}
			$strIn = inQueryBuilder($wids) ;

			$sql = "DELETE FROM window WHERE screen_id='$id' AND id NOT IN($strIn)" ;
			$this->db->execute($sql) ;
			//update windows
			
			foreach( $this->fields['txtTop'] as $k => $something )
			{
				$window = array(
					'screen_id' => $id,
					'name' => $this->fields['txtName'][$k],
					'x' => $this->fields['txtLeft'][$k],
					'y' => $this->fields['txtTop'][$k],
					'w' => $this->fields['txtWidth'][$k],
					'h' => $this->fields['txtHeight'][$k],
					'font_family' => $this->fields['txtFontFamily'][$k],
					'font_weight' => $this->fields['txtBold'][$k],
					'font_style' => $this->fields['txtItalic'][$k],
					'text_decoration' => $this->fields['txtUnderline'][$k],
					'font_size' => $this->fields['txtFontSize'][$k],
					'background' => addslashes( toSingleQuote( urldecode( $this->fields['txtBackground'][$k] ) ) ),
					'text_color' => $this->fields['txtColor'][$k],
				) ;
				$wid = $this->fields['txtId'][$k] ;
				if( intval($wid) )
				{
					$this->window_model->update($window, array('id' => $wid )) ;
				}
				else
				{
					$this->window_model->insert($window) ;
				}
			}
			$this->db->commitTrans() ;
			return new JStatus(true, 'Successfully saved', array('__id' => $id)) ;
			 
		}
		 
        $this->db->rollbackTrans();
		return new JStatus(false, 'Unable to update screen details') ;
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