<?php
class Transferhistory extends Controller
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
		if( @$this->fields['hidPage'] == 'location' )
		{
			$this->listlocations(1) ;
		}
		else
		{
			$this->listtable() ;
		}
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

		if( ! $filter )
		{
			$filter = $this->input->request('hid-searchq') ;
		}
		$cond = '' ;
		if( $filter )
		{
			$cond = " AND (i.name LIKE '%$filter%')" ;
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
			$sort = " ORDER BY td.id DESC " ;
		}

		//location restriction {
		global $QFA ;
		if( $QFA['location.id'] )
		{
			$cond .= " AND ( df.id IN(" . $QFA['location.inquery'] . ") OR dt.id IN(" . $QFA['location.inquery'] . ") ) " ;
		}
		//}

		//}
		$itmId = @$this->getArg('itmId') ;
		
		$cond .= " AND td.item_id = '$itmId' " ;
		$groupby = " GROUP BY td.id " ;

		$sql = "SELECT dt.name AS to_department, df.name AS from_department, td.quantity, t.id, t.dt, df.id as from_id, dt.id AS to_id, lf.name as from_location, lt.name as to_location FROM transfer_detail td 
				INNER JOIN transfer t ON td.transfer_id = t.id
				INNER JOIN item i ON i.id = td.item_id
				LEFT JOIN department df ON df.id = t.from_department_id 
				LEFT JOIN department dt ON dt.id = t.to_department_id 
				
				LEFT JOIN location lf ON lf.id = df.location_id
				LEFT JOIN location lt ON lt.id = dt.location_id

				LEFT JOIN employee e ON e.id = t.createby
				WHERE 1 $cond $groupby $sort" ;

		$sqlcnt = "SELECT COUNT(*) as cnt FROM( SELECT t.id FROM transfer_detail td 
				INNER JOIN transfer t ON td.transfer_id = t.id
				INNER JOIN item i ON i.id = td.item_id
				LEFT JOIN department df ON df.id = t.from_department_id 
				LEFT JOIN department dt ON dt.id = t.to_department_id 
				
				LEFT JOIN location lf ON lf.id = df.location_id
				LEFT JOIN location lt ON lt.id = dt.location_id
				
				LEFT JOIN employee e ON e.id = t.createby
				WHERE 1 $cond $groupby  ) AS a" ;

		$urlPart = '' ;
		if( $itmId )
		{
			$urlPart = "?arg-gen-itmId=" . $itmId ;
		}
		$url = siteUrl('transferhistory/listtable/' . $urlPart ) ;

		$this->vars['pager_url'] = $url ;

		if( $this->ifCsvExport() )
        {
			$order = array() ;
			$order['from_department'] = 'From Department' ;
			if( showBranch() )
			{
				$order['from_location'] = 'From Location' ;
			}
			$order['to_department'] = 'To Department' ;
			if( showBranch() )
			{
				$order['to_location'] = 'To Location' ;
			}
			$order['dt'] = 'Date' ;
			$order['quantity'] = 'Quantity' ;

			$filename = 'history' . Date('Ymdhi') . '.csv' ;
			$this->exportCsv($sql, $order, null ,$header=null, $filename) ;
            return;
		}

		$result = $this->pagination->pager($sql, $sqlcnt, $url, 'idListArea' . get_class($this), $page) ;
		//render page with default template file.
		parent::defListtable($result) ;
	}
	function listlocations($page=1)
	{
		$this->loadLibrary('pagination.php') ;
		$filter = $this->input->request('searchq') ;

		$catid = $this->input->request('searchq-category');
		$dept_loc_id = $this->input->request('searchq-department');

		$cond = '' ;
		if ($filter)
		{
			$cond = " AND (i.name LIKE '%$filter%' OR c.name LIKE '%$filter%')";
		}
		if( $catid )
		{
			$cond .= " AND c.id='$catid'" ;
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
			$cond .= " AND d.id IN(" . $QFA['location.inquery'] . ")" ;
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

		$sql = "SELECT department_id, df.name AS from_department, SUM(s.quantity) AS quantity, df.name AS from_department FROM stock s 
				INNER JOIN department df ON df.id = s.department_id 
				LEFT JOIN location l ON l.id=df.location_id
				INNER JOIN item i ON i.id = s.item_id
				LEFT JOIN category c on c.id = i.category_id
				WHERE 1 $cond $sort" ;

		$sqlcnt = "SELECT COUNT(*) AS cnt FROM stock s 
				INNER JOIN department df ON df.id = s.department_id 
				LEFT JOIN location l ON l.id=df.location_id
				INNER JOIN item i ON i.id = s.item_id
				LEFT JOIN category c on c.id = i.category_id
				WHERE 1 $cond" ;

		$urlPart = '' ;
		if( $itmId )
		{
			$urlPart = "?arg-gen-itmId=" . $itmId ;
		}
		$url = siteUrl('transferhistory/listlocations/' . $urlPart ) ;

		$this->vars['pager_url'] = $url ;

		$result = $this->pagination->pager($sql, $sqlcnt, $url, 'idListArea' . get_class($this), $page) ;
 
		$jstat = new JStatus(false, '') ;
		ob_start() ;
		$this->loadView('transferhistory_location_table', $result );
		$jstat->data['idContentAreaBig'] = ob_get_clean() ;	
		$this->statusResponse($jstat) ;
	}

	function listaction()
	{
	    $this->search();
	}
}

?>