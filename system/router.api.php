<?php
//api exists ? {
$apiapp		= $_REQUEST['app'] ;
$apimodule	= $_REQUEST['module'] ;
$apiaction	= $_REQUEST['action'] ;

global $QFA ;

$sqlapiinfo = "SELECT * FROM api_map WHERE am_app='$apiapp' AND am_module='$apimodule'" ;
$apiinfo = $QFA['dbmain']->fetchRow($sqlapiinfo) ;

/**
 * App directory name end with directory seperator
 */
define('QFS_APP', strtolower($apiapp). '/');

$REQUEST_URI = QFS_BOOT_FILE . '/' . $apiinfo['am_path'] ;
$REQUEST_URI .= '/api' . $apiaction ; 
//}

//is user allowed.. ? {
$apikey = $_REQUEST['apikey'] ;
$sqlapi = "SELECT a.au_emp_id, a.au_enabled, u.usr_grp_code, u.emp_username FROM employee u
		INNER JOIN api_users a ON a.au_emp_id=u.emp_id AND a.au_key='$apikey'" ;

$apirec = $QFA['dbmain']->fetchRow($sqlapi) ;
if( ! $apirec['api_emp_id'] && ! $apiinfo['am_public'] )
{
	return false ;//TODO; send api error response..
}
//} is user allowed..
//{{login user automatically..
require_once 'system/library/session.php';
$obj = new Session ;
$obj->set('usr_id', $apirec['am_emp_id'] ) ;
$obj->set('usr_grp_code', $apirec['usr_grp_code'] ) ;
$obj->set('usr_alias', $apirec['emp_username'] ) ;
//}
$qs = $REQUEST_URI ;

//get file and query string
$fq = explode(QFS_BOOT_FILE, $qs);

$f = QFS_BOOT_FILE;
$q = (isset($fq[1]) ? $fq[1] : '' );

//strip get arguments
$pos = strpos($q, '?');
if ($pos !== false)
{
	$q = substr($q, 0, $pos);
}
//split controller, method and arguments.
$q = trim($q, '? /\\');
$cma = explode('/', $q);

//get controller
$c = '';
if (count($cma) > 0)
{
	$c = ( isset($cma[0]) ? $cma[0] : '' );
	if ($c)
	{
		$c .= '.php';
	}
}
if (!$c)
{
	$c = QFS_DEFAULT_FILE;
}
//get method
$m = '';
if (count($cma) > 1)
{
	$m = ( isset($cma[1]) ? $cma[1] : '' );
}
if (!$m)
{
	$m = 'index';
}
//get args
$a = array();
if (count($cma > 2))
{
	$a = array_slice($cma, 2);
}

//open target controller..
$cfile = findFile($c, 'controllers') ;// QFS_APP . 'controllers/' . $c;

if (!file_exists($cfile))
{
	$module = findFile($cfile, 'modules') ;
	if( $module )
	{
		$cfile = dirname($module) .'/controllers/' . basename($m, '.php') . '.php' ;
		$m = array_shift($a) ;
		if( ! $m )
		{
			$m = 'index' ;
		}
	}
}

if (!file_exists($cfile))
{
	redirect('qerror/display/' . 404) ;
	return;
}
include $cfile;

//build object
$oname = basename($cfile, '.php');
$o = new $oname ;

//set base path..

if( method_exists($o, 'setBaseDir') )
{
	call_user_func_array(array($o, 'setBaseDir'), array(dirname(dirname($cfile))))  ;
}

//query core params..
//{
global $QFL_KEYS ;
$QFL_KEYS = array() ;

$mylangid = getSettings('language', 'mylanguage') ;

if( $mylangid)
{
	$sql = "SELECT lng_file FROM languages WHERE lng_id='$mylangid' AND lng_activated=1" ;
	global $QFA;
	$mylanguage = $QFA['dbmain']->scalarField($sql) ;
	if( $mylanguage )
	{
		$mylanguage = trim($mylanguage) ;

		$QFL_KEYS = @include fileUrl('data/languages/' . $mylanguage) ;
	}
	if( !is_array($QFL_KEYS))
	{
		$QFL_KEYS = array() ;
	}
}
//}

//call target method..
if (method_exists($o, $m))
{
	$canCall = false ;
	//auth test
	if ( ! method_exists($o, 'accessAllowed') )
	{
		//call immediately
		$canCall = true ;
	}
	else
	{
		//check validation, then contirnue.
		if( call_user_func_array(array($o, 'accessAllowed'), array($oname, $m, $a)) )
		{
			$canCall = true ;
		}
		else
		{
			if( isset($_REQUEST['ajaxquery']) )
			{
				header('HTTP/1.0 403 Forbidden') ;
				echo "Access denied, Please try login again." ;
				return ;
			}
			redirect('qerror/display/' . 403) ;
			return ;
		}
	}

	$QFA['_ci'] = array(
		'method' => $m,
		'controller_name' => $oname,
		'controller' => $o,
		'args' => $a,
		'request_uri' => $_SERVER['REQUEST_URI'],
		'request_method' => $_SERVER['REQUEST_METHOD'],
		'ip' => $_SERVER["REMOTE_ADDR"]
	);
	
	$QFA['dbmain']->setLogger(new Log()) ;

	if( $canCall )
	{
		call_user_func_array(array($o, $m), $a);
	}
}
else
{
	trigger_error('Url not recognized.') ;
}
//DONE.
?>