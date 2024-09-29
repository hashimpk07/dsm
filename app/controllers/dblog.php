<?php
class Dblog extends Controller
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
		$this->loadModel('dblog_model') ;
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
			$cond = " AND (m.log_data LIKE '%$filter%' OR e.emp_name LIKE '%$filter%') " ;
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
			$sort = ' ORDER BY m.log_id DESC ' ;
		}
		//}
		$sql    = "SELECT m.log_id, m.log_emp_id, m.log_action, m.log_data, m.log_dt, e.emp_name FROM db_log  m  
                            inner join  employee  e  on e.emp_id = m.log_emp_id 
		                       WHERE  1=1  $cond $sort" ;
                               
                              
                               		$sqlcnt = "SELECT COUNT(*) as total FROM db_log  m  
                            inner join  employee  e  on e.emp_id = m.log_emp_id 
		                       WHERE  1=1  $cond" ;
		$url = siteUrl('dblog/listtable/') ;

		$this->vars['pager_url'] = $url ;
		if( $this->ifCsvExport() )
											{
												$order = array(
												'emp_name' => 'Employee',
												'log_action'=>'Action',
												'log_data'=>'Data',
												'log_dt'=>'Date',
												) ;

												$this->exportCsv($sql,$order) ;
												return;
            
											}
		
		
		$result = $this->pagination->pager($sql, $sqlcnt, $url, 'idListArea' . get_class($this), $page) ;
		//render page with default template file.
		parent::defListtable($result) ;
	}
	/**
	
		/**
	 * Display indivitual details
	 */
	function view($id)
{
          $rec = $this->dblog_model->getDetails($id) ;
        $this->loadView('dblog_view.php', array('result' => $rec)) ;
	}
	/**
	 * Mark a record as deleted.
	 * 
	 * @param int $id id to remove
	 * @return bool
	 */
	
	/**
	 * Buld action handler
	 * 
	 * @return boolean true on succes
	 */
	
        
}
?>