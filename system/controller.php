<?php

class Controller extends Mvc
{
	private $title ;
    
	function __construct()
	{
		parent::__construct() ;

		global $QFC;
		$QFC = $this;

		$this->doAutoload() ;

		$this->layout = new Layout();
		$this->loadFields() ;
	}
	/**
	 * Page title
	 * @return string
	 */
	function getPageTitle()
	{
		if( $this->title )
		{
			return $this->title ;
		}
		return ucfirst(get_class($this)) ;
	}
	/**
	 * Set current page title.
	 * 
	 * @param string $title title
	 */
	function setPageTitle($title)
	{
		$this->title = $title ;
	}
	/**
	 * A default index function handler.
	 */
	function index()
	{
		$this->defIndex();
	}
    /**
     * Check whether the export function called.
     * 
     * @return boolean true on success
     */
    function ifCsvExport()
    {
        if( isset($this->fields['btnSearchCsvExport']) )
        {
            return true ;
        }
        return false;
    }
    /**
     * Export a query result as csv, etc..
     * 
     * @param string $sql sql query to fetch
     * @param array $columns filter columns
     * @param string $header header values if any
     */
    function exportCsv($sql, $columns = null, $header_field, $header = null, $filename=null )
    {
		if( is_array($sql) )
		{
			$records = $sql ;
		}
		else
		{
			$records = $this->db->fetchRowSet($sql, 'assoc') ;
		}
		return array2Csv($records, $columns, $header_field, $header, $filename) ;
    }
	function exportPdf($file, $view, $data)
	{
		//move to a function
		ob_start() ;
//		$this->layout->theme = 'tti/print.php';
		
		$this->layout->loadView($view, $data ) ;
		$html = ob_get_clean() ;
		//pdf out function
		require_once( fileUrl( "assets/packages/dompdf/dompdf_config.inc.php"));
//			require fileUrl( '/../assets/packages/dompdf/dompdf.php');
		$dompdf = new DOMPDF();
		$dompdf->set_paper("A4", "portrait");
		$dompdf->load_html($html);
		$dompdf->render();
		$dompdf->stream($file);
//		echo $html ;
	}
	/**
	 * Dispaly helper
	 */
	function defIndex()
	{
		ob_start();
		$this->listall();
		$ob = ob_get_clean();

		$this->layout->loadData($ob);
	}
	/**
	 * Render listall page with layout.
	 * 
	 * @return bool
	 */
	function listall()
	{
		$this->defListall();
	}
	/**
	 * Render display table.
	 * 
	 * @param int $page page no
	 * @param array $vars
	 * @return bool
	 */
	function listtable()
	{
		$this->defListtable() ;
	}
 
        
	/**
	 * List all records helper.
	 * 
	 * @param int $page page no
	 * @param array $vars
	 * @return bool
	 */
	function defListall($vars = array())
	{
		$class = strtolower(get_class($this));
		$view = $class . '.php';
		ob_start() ;
		$this->listtable() ;
		$vars['listtable'] = ob_get_clean() ;
		return $this->loadView($view, $vars);
	}
	/**
	 * Render view file named "classname_table.php" file and return its result. The result does not includes actual view file.
	 * 
	 * @param array $vars
	 * @return bool
	 */
	function defListtable($vars = array())
	{
		$class = strtolower(get_class($this));
		$view = $class . '_table.php';

		return $this->loadView($view, $vars);
	}

	/**
	 * Show the add form. The view name will be treated as "currentclassname_add.php".
	 */
	function add()
	{
		$this->defAdd();
	}

	/**
	 * Add form display helper
	 * 
	 * @param array $vars
	 * @return bool
	 */
	function defAdd($vars = array())
	{
		$class = strtolower(get_class($this));
		$view = $class . '_add.php';

		return $this->loadView($view, $vars);
	}
	/**
	 * Show edit form with values filled in. The view name will be treated as "currentclassname_add.php".
	 * 
	 * @param int|string $keyValue primary key value in table.
	 */
	function edit($keyValue)
	{
		$this->defEdit($keyValue);
	}

	/**
	 * Edit form helper.
	 * 
	 * @param string | int $keyValue key value
	 * @param string $keyColumnName key column name
	 * @param array $vars view variables
	 * @return boool
	 */
	function defEdit($keyValue, $keyColumnName = 'id', $vars = array())
	{
		$class = strtolower(get_class($this));
		$view = $class . '_add.php';

		$model = $class . '_model';
		$this->loadModel($model);

		$record = $this->{$model}->load(array($keyColumnName => $keyValue));

		$vars['result'] = $record;
		return $this->loadView($view, $vars);
	}
	/**
	 * Show details about a record. The view name will be treated as "currentclassname_view.php".
	 * 
	 * @param int|string $keyValue primary key value in table.
	 */
	function view($keyValue)
	{
		$this->defView($keyValue);
	}

	/**
	 * View details helper
	 * 
	 * @param string | int $keyValue
	 * @param string $keyColumnName
	 * @param array $vars
	 * @return bool
	 */
	function defView($keyValue, $keyColumnName = 'id', $vars = array())
	{
		$class = strtolower(get_class($this));
		$view = $class . '_view.php';

		$model = $class . '_model';
		$this->loadModel($model);

		$record = $this->{$model}->load(array($keyColumnName => $keyValue));

		$vars['result'] = $record;
		return $this->loadView($view, $vars);
	}
	/**
	 * Delete a record by matching primary key. 
	 * 
	 * @param int|string $keyValue primary key value in table.
	 */
	function delete($keyValue)
	{
		$this->defDelete($keyValue);
	}

	/**
	 * Delete record matching key value.
	 * 
	 * @param type $keyValue key value
	 * @param type $keyColumnName key colun name
	 */
	function defDelete($keyValue, $keyColumnName = 'id', $deleteFlagName = 'deleted', $silent = false)
	{
		$class = strtolower(get_class($this));
		$model = $class . '_model';
		$this->loadModel($model);

		$status = $this->{$model}->update(array($deleteFlagName => 1), array($keyColumnName => $keyValue));

		if( ! $silent )
		{
			$this->statusResponse( (($status) ? 'OK' : 'Fail'), ( ($status) ? 'Item deleted successfully.' : 'Item not removed.'), array('_id' => $keyValue)) ;
		}

		return $status;
	}
	/**
	 * Print a view using template. Override this function to use custom templates.
	 * 
	 * @param string $function print a function
	 */
	function viewPrint($args)
	{
		$this->layout->theme = 'tti/view_print.php';

		if( method_exists($this, 'view') )
		{
			ob_start() ;
			call_user_func_array(array($this, 'view'), array($args) ) ;
			$contents = ob_get_clean() ;

			$this->layout->loadData($contents) ;
			return true ;
		}
		return false ;
	}
}

?>