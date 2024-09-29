<?php
class Transferlocation extends Controller
{
	public function __construct()	{

		$acls = array(
			'allow' => array(
						'S, E' => '*' ),
			'deny' => array(),
			'order' => 'AD' //Allow, Then Deny (Options are "DA" or "AD")
		);
		$this->acl($acls) ;

		parent::__construct();

		$this->loadLibrary('alldata') ;

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
		$this->setArg('itmId', @$this->getArg('itmId') ) ;
		//print_r(@$this->getArg('itmSl'));
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

		$dept_loc_id = $this->input->request('searchq-department');

		$cond = '' ;
		if ($filter)
		{
			$cond = " AND (i.name LIKE '%$filter%' OR c.name LIKE '%$filter%')";
		}
		
		if( $dept_loc_id )
		{
			list($typo, $id) = explode('@', $dept_loc_id) ;
			if( strtoupper($typo) == 'D' )
			{
				$cond .= " AND df.id='$id' " ;
			}
			else if( strtoupper($typo) == 'L' )
			{
				$cond .= " AND l.id='$id' " ;
			}
		}
		//location restriction {
		global $QFA ;
		if( $QFA['location.id'] )
		{
			$cond .= " AND df.id IN(" . $QFA['location.inquery'] . ")" ;
		}
		//}

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
			$sort = " " ;
		}

		//}
		$itmId = @$this->getArg('itmId') ;
		
		$cond .= " AND s.item_id='$itmId' " ;

		$sql = "SELECT department_id, df.name AS from_department, SUM(s.quantity) AS quantity, l.name as location FROM stock s 
				INNER JOIN department df ON df.id = s.department_id 
				INNER JOIN item i ON i.id = s.item_id
				LEFT JOIN location l ON l.id=df.location_id
				WHERE 1 $cond GROUP BY df.id $sort " ;

		
		$sqlcnt = "SELECT COUNT(*) AS cnt FROM stock s 
				INNER JOIN department df ON df.id = s.department_id 
				INNER JOIN item i ON i.id = s.item_id
				LEFT JOIN location l ON l.id=df.location_id
				WHERE 1 $cond GROUP BY df.id " ;

		$urlPart = '' ;
		if( $itmId )
		{
			$urlPart = "?arg-gen-itmId=" . $itmId ;
		}
		$url = siteUrl('transferlocation/listtable' . $urlPart ) ;

		$this->vars['pager_url'] = $url ;

		if( $this->ifCsvExport() )
        {
			$order = array() ;
			$order['from_department'] = 'Department' ;
			if( showBranch() )
			{
				$order['location'] = 'Location' ;
			}
			$order['quantity'] = 'Quantity' ;

			$filename = 'location' . Date('Ymdhi') . '.csv' ;
			$this->exportCsv($sql, $order, null ,$header=null, $filename) ;
            return;
		}

		$result = $this->pagination->pager($sql, $sqlcnt, $url, 'idListArea' . get_class($this), $page) ;
 
		$this->loadView('transferlocation_table', $result );
	}

	function listaction()
	{
	    $this->search();
	}
}

?>