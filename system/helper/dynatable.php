<?php

/*--
	Table
		class	= tbl-dyna
		id		= idDynaTable{ClassName}
	Table Tr
		class	= dyna-tr
		id		= idDynaTableTr{@DYN.emp_id}
	Table Tr Td
		class	= dyna-td

	Template Data
		var		= gDynaTemplate{ClassName}

	Dynamic Codes
		{@DYN.id} = Row Serial No
		{@DYN.add} = Add Button
		{@DYN.remove} = Remove Button
		{@DYN.clear} = Clear Button
--*/
class DynamicTable
{
	/**
	 * table id/name
	 * 
	 * @var string 
	 */
	public $tableName ;

	/**
	 * constructor
	 * 
	 * @param string $name
	 */
	public function __construct($name = '')
	{
		$this->tableName = $name ;
	}
	/**
	 * string table name
	 * 
	 * @param string $name
	 */
	public function setTableName($name)
	{
		$this->tableName = $name ;
	}
	/**
	 * get table name in use.
	 * 
	 * @return string
	 */
	function getName()
	{
		return $this->tableName ;
	}
	/**
	 * get add button code
	 * 
	 * @return string
	 */
	function getAddButton($attrs = array())
	{
		if( ! isset($attrs['class']) )
		{
			$attrs['class'] = 'dyna-icon flaticon flaticon-plus' ;
		}
		$attrs['onclick'] = (isset($attrs['onclick']) ? $attrs['onclick'] . ";" : '') . 'dynaTable_Add(\'' . $this->tableName . '\')' ;
		
		$str = ' ' ;
		foreach( $attrs as $k => $v )
		{
			$str .= "$k = \"$v\" " ;
		}

		return "<span $str ></span>" ;
	}
	/**
	 * Get remove button.
	 * 
	 * @param string $index
	 * @return string
	 */
	function getRemoveButton($index, $attrs = array())
	{
		if( ! isset($attrs['class']) )
		{
			$attrs['class'] = 'dyna-icon flaticon flaticon-minus' ;
		}
		$attrs['onclick'] = (isset($attrs['onclick']) ? $attrs['onclick'] . ";" : '') . 'dynaTable_Remove(\'' . $this->tableName . '\', \'' . $index . '\')' ;
		
		$str = ' ' ;
		foreach( $attrs as $k => $v )
		{
			$str .= "$k = \"$v\" " ;
		}

		return "<span $str ></span>" ;
	}
	function getClearButton($attrs = array())
	{
		if( ! isset($attrs['class']) )
		{
			$attrs['class'] = 'dyna-icon flaticon flaticon-trash' ;
		}
		$attrs['onclick'] = (isset($attrs['onclick']) ? $attrs['onclick'] . ";" : '') . 'dynaTable_Clear(\'' . $this->tableName . '\')' ;
		
		$str = ' ' ;
		foreach( $attrs as $k => $v )
		{
			$str .= "$k = \"$v\" " ;
		}

		return "<span $str ></span>" ;
	}
	function initTemplate($obj, $viewname )
	{
		if( ! $this->tableName )
		{
			trigger_error('Table name not specified (Use constructor).') ;
		}

		$str = '<script type="text/javascript">' ;
		ob_start() ;
		$obj->loadView($viewname) ;
		$view = ob_get_clean() ;
		$str .= 'var gDynaTemplate' . $this->tableName . '= "' . urlencode($view) . '"';
		$str .= '</script>' ;
		return $str ;
	}
	function attachRows($obj, $viewname, $resultSet, $mode )
	{
		$rowstr = '' ;
		if( $mode == 'edit' )
		{
			if( is_array($resultSet) )
			{
				foreach( $resultSet as $row )
				{
					ob_start() ;
					$obj->loadView($viewname, array('othis'=> $this, 'row' => $row)) ;
					$strOne = ob_get_clean() ;

					foreach( $row as $k => $v )
					{
						$strOne = str_ireplace('{@DYN.COL.' . $k . '}',  $v, $strOne) ;	
					}
					//Replace pending col values..
					$strOne = str_ireplace('/\{\@DYN\.COL\.[a-zA-Z_1-9\x20\.]+\}', '', $strOne) ;

					$strOne = urldecode($strOne) ;
					$id = 'd' . rand(9999, 999999999) ;
					//Id
					$strOne = str_ireplace('{@DYN.id}' , $id, $strOne) ;
					//Remove Button
					$strOne = str_ireplace('{@DYN.remove}' , $this->getRemoveButton($id), $strOne) ;
					//Clear Button
					$strOne = str_ireplace('{@DYN.clear}' , $this->getClearButton(), $strOne) ;
					//Add Button
					$strOne = str_ireplace('{@DYN.add}' , $this->getAddButton(), $strOne) ;

					$rowstr .= $strOne ;
				}
			}
		}
		return $rowstr ;
	}
}
/**
 * 
 * @param type $obj
 * @param type $name
 * @param type $file
 */
function dynaInit($obj, $name, $file)
{
	$str = "var $name='" ;
	ob_start() ;
	$obj->loadView($file) ;
	$data = ob_get_clean() ;
	$str .= urlencode($data) ;
	$str .= "';";
	
	echo "<script type='text/javascript'>eval(\"$str\");</script>" ;	
}

function dropJsVar($var, $value)
{
	echo "<script type='text/javascript'>var $var = \"$value\";</script>" ;
}

?>