<?php
class Screen_log extends Controller
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
		$this->loadModel('screen_log_model') ;
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
			$cond = " AND (b.name LIKE '%$filter%' OR s.name LIKE '%$filter%')" ;
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
			$sort = ' ORDER BY sl.id DESC ' ;
		}
		
		//Branch only data..
		$branchId = branchEmployee() ;
		if( $branchId )
		{
			$cond .= " AND sl.branch_id='$branchId' " ; 
		}

		$sql = "SELECT sl.id, b.name as branch, sl.up_dt, sl.ping_dt, s.name as screen FROM screen_log sl
				LEFT JOIN screen s ON s.id = sl.screen_id
				LEFT JOIN branch b ON b.id = sl.branch_id
				WHERE 1 $cond $sort" ;
		
		$sqlcnt ="SELECT COUNT(*) as cnt FROM screen_log sl
				LEFT JOIN screen s ON s.id = sl.screen_id
				LEFT JOIN branch b ON b.id = sl.branch_id
				WHERE 1 $cond " ;

		$url = siteUrl('screen_log/listtable/') ;

        $this->vars['pager_url'] = $url ;
		if( $this->ifCsvExport() )
		{
			$order = array(
				'Branch' => 'branch',
				'Up Time' => 'up_dt',
				'Down Time' => 'ping_dt',
				'Screen'=>'screen',
			) ;

			$this->exportCsv($sql, $order) ;
			return;
		}
		
		$result = $this->pagination->pager($sql, $sqlcnt, $url, 'idListArea' . get_class($this), $page) ;
		//render page with default template file.
		parent::defListtable($result) ;
	}
}
?>