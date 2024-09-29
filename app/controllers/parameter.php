<?php
class Parameter extends Controller
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
		$this->loadModel('parameter_model') ;
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
		$this->add() ;
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
		$this->vars['url'] = siteUrl('parameter/page') ;
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
		$parameter = array(
				
				'value' => $this->fields['txtColor'],
		) ;
			
		if( $this->parameter_model->update($parameter,array('code' =>'PRM_THEME_BG')) )
		{			
			
			$this->db->commitTrans() ;
			return new JStatus(true, 'Successfully saved', array('__script' => 'window.location.reload();')) ;
			
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
			
		
			$this->statusResponse($jstat) ;
			return ;
		}
		$this->vars['url'] = siteUrl('parameter/add' ) ;
		$this->vars['result'] = $this->parameter_model->getDetailscode() ;
		$this->loadView('parameter_add.php') ;
	
	}
	
	
	
	
}
?>