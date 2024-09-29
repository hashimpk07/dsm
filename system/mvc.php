<?php

class Mvc
{
	/**
	 * Json status holder.
	 * 
	 * @var object
	 */
	public $jstatus ;
	/**
	 * Access Control List
	 * 
	 * @var acl
	 */
	public $acl ;
	public $priv ;
	/**
	 * Variables to be passed to view and layouts.
	 * 
	 * @var array
	 */
	public $vars = array();
	/**
	 * Arguments
	 * 
	 * @var array
	 */
	private $arg = array();
	/**
	 * Layout object
	 * 
	 * @var Layout
	 */
	public $layout;

	/**
	 * @var KMysql $db
	 */
	public $db;
	/**
	 * Holds submitted form data.
	 * 
	 * @var array input array
	 */
	public $fields = array() ;

	function __construct()
	{
		$this->jstatus = new JStatus ;
		//attach core files..
		global $QFA;

		foreach ($QFA as $k => $v)
		{
			$this->{$k} = $v;
		}
	}
	function setArg( $name, $value, $base = null)
	{
		$key = 'arg' ;
		if( ! $base )
		{
			$base = 'gen' ;
		}
		$key .= '-' . $base . '-' . $name ;

		$this->arg[$key] = $value ;
	}
	function getArg($name, $base=null)
	{
		$key = 'arg' ;
		if( ! $base )
		{
			$base = 'gen' ;
		}
		$key .= '-' . $base . '-' . $name ;

		if( isset($this->arg[$key]) )
		{
			return $this->arg[$key] ;
		}
		return false ;
	}
	function formFields()
	{
		$str = '' ;
		foreach( $this->arg as $k => $v )
		{
			if( $str )
			{
				$str .= '' ;
			}
			$str .= "<input type='hidden' name='$k' value='$v' />" ;
		}
		return $str ;
	}
	function searchFields()
	{
		$str = '';
		foreach( $this->fields as $k => $v )
		{
			if( !is_array($k) && !is_array($v) )
			{
				if( strtolower($k) == 'searchq' )
				{
					$str .= "<input type='hidden' name='$k' value='$v' />" ;
				}
			}
		}
		return $str ;
	}
	function urlFields()
	{
		$str = '' ;
		foreach( $this->arg as $k => $v )
		{
			if( $str )
			{
				$str .= '&' ;
			}
			$str .= "$k=$v" ;
		}
		return $str ;
	}
	/**
	 * 
	 * @param type $input input g, p or r, OR get, post or request
	 */
	function loadFields($input = null)
	{		
		if( ! isset($this->input) )
		{
			global $QFA ;
			$this->input = $QFA['input'] ;
		}
		if( ! $input && isset($this->input->request['apikey']) )
		{
			$input = 'r' ;
		}
		switch (strtolower($input))
		{
			case 'g' :
			case 'get' :
					$this->fields = $this->input->get ;
				break ;
			case 'p' :
			case 'post' :
					$this->fields = $this->input->post ;
				break ;
			case 'r' :
			case 'request' :
			default :
					$this->fields = $this->input->request ;
				break ;
		}
		
		//load arguments {
		foreach( $this->fields as $k => $v )
		{
			if( stripos($k, 'arg-') === 0 )
			{
				$this->arg[$k] = $v ;
			}
		}
		//}
	}
	/**
	 * Add an authentication rule to table. <br/>
	 *  <br/>
	 * Format :  <br/>
	 * array( 'usr_grp_code' => 'allowed_methods' ) ; <br/>
	 *  <br/>
	 * if symbol * given frm key (user group code) means all employee. <br/>
	 * if sysmbol * given for value (allowed methods) means all methods. <br/>
	 * employee and methods can be comma seperated to enter multiple employee, and multiple methods. <br/>
	 * eg: <br/>
	 * array( 'A,E' => '*' ) : This method means Admin and employee can access every methods. <br/>
	 * array( '*' => '*' ) : This method means every one can access everything. <br/>
	 * array( '*' => 'add, edit' ) : This method means every one can access add and edit. <br/>
	 * array( 'E' => 'delete' ) : This method means employee can access delete. <br/>
	 * @param array $acl_allowed_denied allowed list
	 */
	public function acl($acl_allowed_denied)
	{
		$this->acl = $acl_allowed_denied ;
	}
	public function getAcl()
	{
		return $this->acl ;
	}
	public function priv($priv_allowed)
	{
		$this->priv = $priv_allowed ;
	}
	/**
	 */
	public function accessAllowed($class = null, $method = null, $args = null)
	{
		$USER_PERM = false ;
		$GRP_ALLOW = false ;
		$GRP_DENY = false ;
		$USER_PERM_REQUIRED = true ;

		$classObj = $this->loadController($class) ;
		$acl = $classObj->getAcl() ;

		if( isset($acl['user']) )
		{
			$USER_PERM_REQUIRED = (($acl['user']) ? true : false) ;
		}
		//do priv check
		$this->loadLibrary('qprivileges') ;
		if( ! $class )
		{
			$class = get_class($this) ;
		}

		$codeset = array() ;
		$codefunc = strtolower($class) . '-' . strtolower($method) ;
		$codeset[] = $codefunc ;
		//aclaction
		foreach( $_REQUEST as $k => $v )
		{
			if( stripos($k, 'aclaction-') === 0 )
			{
				$actionparts = explode('-', $k) ;
				$action = end( $actionparts ) ;
				$codeset[] = $codefunc . '@' . strtolower($action) ;
			}
		}
		//}

		$lastcode = $codefunc ;
		if( is_array($args) )
		{
			foreach( $args as $argval )
			{
				if( ! $argval )
				{
					continue;
				}
				if( $argval )
				{
					$lastcode = $lastcode . '-' . $argval ;
					$codeset[] = $lastcode ;
				}
			}
		}

		$USER_PERM = $this->qprivileges->check($codeset) ;
		//group access check..
		$this->loadLibrary('session') ;
		$user_group_code = $this->session->get('usr_grp_code') ;

		//user prvilege check..
		$allowed = array() ;
		if(isset($acl['allow']))
		{
			$allowed = $acl['allow'] ;
		}
		$mafinal = array() ;

		if( count($allowed) > 0 )
		{
			foreach ($allowed as $uset =>$mset )
			{
				if( stripos($uset, $user_group_code) !== false || $uset === '*' )
				{
					$ma = explode(',', $mset ) ;
					foreach( $ma as $m1 )
					{
						$mafinal[] = trim($m1) ;
					}
				}
			}
			if( in_array($method, $mafinal) )
			{
				$GRP_ALLOW = true ;
			}
			else if( in_array('*', $mafinal) )
			{
				$GRP_ALLOW = true ;
			}
		}

		$denied = array() ;
		if(isset( $acl['deny'] ))
		{
			$denied = $acl['deny'] ;
		}
		$mdfinal = array() ;

		if( count($denied) > 0 )
		{
			foreach ($denied as $uset =>$mset )
			{
				if( stripos($uset, $user_group_code) !== false || $uset === '*' )
				{
					$ma = explode(',', $mset ) ;
					foreach( $ma as $m1 )
					{
						$mdfinal[] = trim($m1) ;
					}
				}
			}
			if( in_array($method, $mdfinal) )
			{
				$GRP_DENY = true ;
			}
			else if( in_array('*', $mdfinal) )
			{
				$GRP_DENY = true ;
			}
		}

		$order  = 'DA' ; //All denied by default
		if( isset($acl['order']) )
		{
			$order = $acl['order'] ;
		}
		if( $order )
		{
			if( strtoupper($order) == 'AD' )
			{
				if($GRP_ALLOW && (($USER_PERM_REQUIRED) ? $USER_PERM : 1) )
				{
					if($GRP_DENY)
					{
						return false ;
					}
					//Group deny not mentioned ?
					return true ;
				}
			}
			else if( strtoupper($order) == 'DA' )
			{
				if( $GRP_ALLOW && (($USER_PERM_REQUIRED) ? $USER_PERM : 1) )
				{
					return true ;
				}
			}
		}

		return false ;
	}
	/**
	 * Render a view file
	 * 
	 * @param type $file view file name,which should be displayed
	 * @param type $vars variables to be passed to view ( not available in layout )
	 * @param type $return if this value set true the output will be returned instead of sending it to browser
	 * @return string view result if $return is set true
	 */
	function loadView($file, $vars = array(), $return = false)
	{
		global $QFC;
		if (!is_array($this->vars))
		{
			trigger_error('vars variable is corrupt ($this->vars) ', E_USER_WARNING);
		}
		else
		{
			extract($this->vars);
		}

		$vars['QF'] = $QFC;

		if (is_array($vars))
		{
			extract($vars);
		}
		$filepath = $this->findFile($file, 'views');
		if ($return)
		{
			ob_start();
			include ( $filepath );
			return ob_get_clean();
		}
		return include ( $filepath );
	}
	/**
	 * Return a view file
	 * 
	 * @param type $file view file name,which should be displayed
	 * @param type $vars variables to be passed to view ( not available in layout )
	 * @param type $return if this value set true the output will be returned instead of sending it to browser
	 * @return string view result if $return is set true
	 */
	function getView($file, $vars = array())
	{
		return $this->loadView($file, $vars, true) ;
	}
	/**
	 * Load a model and return its object.
	 * 
	 * @param string $file file to load
	 * @param string $vars constructor variables
	 * @return object model object
	 */
	function loadModel($file, $vars = array(), $return = false)
	{
		$filepath = $this->findFile($file, 'models');
		include_once $filepath;
		$class = basename($filepath, '.php');
		$obj = new $class($vars);
		if (!$return)
		{
			$this->{$class} = $obj;
			$this->injectLibraries($this->{$class});
		}
		return $obj;
	}
	function getModel($file, $vars = array())
	{
		return $this->loadModel($file, $vars, false) ;
	}
	public $baseDir ;

	function setBaseDir( $dirname )
	{
		$this->baseDir = $dirname ;
	}
	/**
	 * Load a module and return its object. A mudule must be in a directory with its module name.
	 * 
	 * @param string $file file to load
	 * @param string $vars constructor variables
	 * @return object model object
	 */
	function loadModule($module, $vars = array(), $return = false)
	{
		$filepath = $this->findFile($module , 'modules');
		include_once $filepath;
		$class = basename($filepath, '.php');
		$obj = new $class($vars);
		if( method_exists($obj, 'setBaseDir') )
		{
			call_user_func_array(array($obj, 'setBaseDir'), array(dirname($filepath)))  ;
		}
		if (!$return)
		{
			$this->{$class} = $obj;
			$this->injectLibraries($this->{$class});
		}
		return $obj;
	}
	function getModule($module, $vars = array())
	{
		return $this->loadModule($module, $vars, true) ;
	}

	/**
	 * Inject esential libraries into an object.
	 * 
	 * @global array $QFA global array contains essential libs
	 * @param object $obj target object
	 */
	private function injectLibraries(&$obj)
	{
		global $QFA;
		foreach ($QFA as $k => $v)
		{
			$obj->{$k} = $v;
		}
	}

	/**
	 * Load a model and return its object.
	 * 
	 * @param string $file file to load
	 * @param string $vars constructor variables
	 * @return object model object
	 */
	function loadLibrary($file, $vars = array(), $return = false)
	{
		$filepath = $this->findFile($file, 'library');
		include_once $filepath;
		$class = basename($filepath, '.php');
		$obj = new $class($vars);
		if (!$return)
		{
			$this->{$class} = $obj;
			$this->injectLibraries($this->{$class});
		}
		return $obj;
	}
	/**
	 * Load a controller and return its object.
	 * 
	 * @param string $file file to load
	 * @param string $vars constructor variables
	 * @return object model object
	 */
	function loadController($file, $vars = array(), $return = false)
	{
		global $QFC ;
		//Backup QFC Object
		$QFC_Temp = $QFC ;

		$filepath = $this->findFile($file, 'controllers') ;
		include_once $filepath ;
		$class = basename($filepath, '.php');
		$obj = new $class($vars);
		if(!$return)
		{
			$this->{$class} = $obj;
			$this->injectLibraries($this->{$class});
		}
		//Restore QFC Object
		$QFC = $QFC_Temp;
		return $obj;
	}
	function getLibrary($file, $vars = array())
	{
		return $this->loadLibrary($file, $vars, true) ;
	}

	/**
	 * Load a helper file
	 * 
	 * @param string $file file to load
	 * @param string $vars constructor variables
	 * @return object model object
	 */
	function loadHelper($file, $vars = array(), $return = false)
	{
		if (is_array($vars))
		{
			extract($vars);
		}
		$filepath = $this->findFile($file, 'helper');
		if ($return)
		{
			ob_start();
			include_once ( $filepath );
			return ob_get_clean();
		}
		return include_once ( $filepath );
	}
	function getHelper($file, $vars = array())
	{
		return $this->loadHelper($file, $vars, true) ;
	}

	/**
	 * Send json response to browser.
	 * 
	 * @param type $status status code. OK or FAIL
	 * @param type $msg detailed status message 
	 * @param type $fields json fields
	 */
	function statusResponse($jstatus = 'OK', $msg = 'Sucess.', $fields = array())
	{
		if(is_object($jstatus) )
		{
			$status = ( ($jstatus->status) ? 'OK' : 'FAIL' ) ;
			$msg = $jstatus->msg ;
			$fields = $jstatus->data ;
		}
		else
		{
			$status = $jstatus ;
		}

		$a = array(
			'status' => $status,
		) ;

        if( ! defined('QF_MODE_API') )
        {
			switch( strtoupper($status) )
			{
				case 'OK' :
						$a['idSuccessMsg'] = $msg ;
					break ;
				case 'FAIL' :
						$a['idFailureMsg'] = $msg ;
					break ;
			}
        }
        else
        {
            $a['message'] = $msg ;
        }
		if(is_array($fields) )
		{
			$a = array_merge($a, $fields) ;
		}
		
		$a['__contentAreaClicked'] = $this->getArg('contentAreaClicked') ;

        if( defined('QF_MODE_API') )
        {
            //Add API root tags..
            if( @$this->fields['respType'] == 'json' )
            {
				jsonResponse($a) ;
			}
            else
            {
                xmlResponse($a) ;
            }
            return ;
        }
		jsonResponse($a) ;
	}
	
	/**
	 * Search for a file in app folder then shared folder after that in system folder
	 * 
	 * @param string $file file name
	 * @param string $subPath sub folder name
	 * @return string filepath
	 */
	function findFile($file, $subPath)
	{
		$lookupList = null ;
		if( $this->baseDir )
		{
			$lookupList = array(
				rtrim($this->baseDir, '/')
			) ;
		}
		return findFile($file, $subPath, $lookupList) ;
	}
	/**
	 * Attach suggested autoload libraries into current class.
	 * 
	 * @global array $APP_CONFIG
	 */
	function doAutoload()
	{
		/**
		 * Autoload things
		 */
		global $APP_CONFIG;

		if (is_array($APP_CONFIG))
		{
			foreach ($APP_CONFIG as $k => $v)
			{
				switch ($k)
				{
					case 'autoload_library' :
						if (is_array($v))
						{
							foreach ($v as $class => $arglist)
							{
								$this->loadLibrary($class, $arglist);
							}
						}
						break;
					case 'autoload_models' :
						if (is_array($v))
						{
							foreach ($v as $class => $arglist)
							{
								$this->loadModel($class, $arglist);
							}
						}
						break;
					case 'autoload_helper' :
						if (is_array($v))
						{
							foreach ($v as $class => $arglist)
							{
								$this->loadHelper($class, $arglist);
							}
						}
						break ;
				}
			}
		}
	}
}
?>