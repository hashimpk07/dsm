<?php

class Stockreport extends Controller
{
	public function __construct()
	{
		$acls = array(
			'allow' => array(
				'*' => '*'),
			'deny' => array(),
			'order' => 'AD' //Allow, Then Deny (Options are "DA" or "AD")
		);
		$this->acl($acls);

		parent::__construct();

		$this->loadLibrary('alldata') ;
		/* @var $this->brokers_model Brokers_model */
		$this->loadModel('stock_model');
	}
	/**
	 * Default function
	 */
	function index()
	{
		//request will be routed to display.
		parent::defIndex();
	}
	/**
	 * All search reach here
	 */
	function search()
	{
		$this->listtable();
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
		parent::defListall();
	}
	/**
	 * Method to retrieve records list table.
	 * 
	 * @param int $page page no.
	 */
	function listtable($page = 1)
	{
		$this->loadLibrary('pagination.php');
		$filter = $this->input->request('searchq') ;

		if (!$filter)
		{
			$filter = $this->input->request('hid-searchq');
		}
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
				$cond .= " AND d.id='$id' " ;
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
		$sort = '';
		if (@$this->input->request['searchq-col'] || @$this->input->request['hid-searchq-col'])
		{
			$sort = ' ORDER BY ' . ( ($this->input->request['searchq-col']) ? $this->input->request['searchq-col'] : @$this->input->request['hid-searchq-col'] );
			if ((@$this->input->request['searchq-sort'] == 'desc') || (@$this->input->request['hid-searchq-sort'] == 'desc'))
			{
				$sort .= ' DESC ';
			}
			else
			{
				$sort .= ' ASC ';
			}
		}
		if (!$sort)
		{
			$sort = ' ORDER BY i.id DESC ';
		}
		$group = " GROUP BY s.item_id " ;
		//}

		$sql = "SELECT i.name AS item, i.id, SUM(s.quantity) as quantity, c.name as category FROM item i
				INNER JOIN stock s ON i.id = s.item_id
				
				INNER JOIN department d ON s.department_id = d.id AND d.type = 'D'
				LEFT JOIN location l ON d.location_id = l.id
				
				LEFT JOIN category c ON c.id = i.category_id
				WHERE i.deleted = '0' $cond $group $sort";

		$sqlcnt = "SELECT COUNT(*) as cnt FROM (SELECT SUM(s.quantity) as stock, i.id FROM item i
				INNER JOIN stock s ON i.id = s.item_id
				
				INNER JOIN department d ON s.department_id = d.id AND d.type = 'D'
				LEFT JOIN location l ON d.location_id = l.id

				LEFT JOIN category c ON c.id = i.category_id
				WHERE i.deleted = '0' $cond $group) AS a";

		$header_field=get_class($this);
		$url = siteUrl('stockreport/listtable/');
		$this->vars['pager_url'] = $url;
		/*if ($this->ifCsvExport())
		{
			$order = array(
				'itm_name' => 'Items',
				'itm_type' => 'Type',
				'cat_name' => 'Category',
				'Quantity' => 'cnt',
			);

			$this->export($sql, $order);
			return;
		}*/
		
		 if( $this->ifCsvExport() )
        {
           $order = array(
               'item' => 'Item',
               'category' => 'Category',
               'quantity' => 'Quantity',
               ) ;
		   $filename = 'stock' . Date('Ymdhi') . '.csv' ;
		   $this->exportCsv($sql, $order, $header_field ,$header=null, $filename) ;
            return;
		}
		//get categories
		$result = $this->pagination->pager($sql, $sqlcnt, $url, 'idListArea' . get_class($this), $page);
		//render page with default template file.
		parent::defListtable($result);
	}
	function listaction()
	{
		$this->search();
	}
}
