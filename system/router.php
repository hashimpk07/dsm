<?php

$qs = $_SERVER['REQUEST_URI'];

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

$fil = strtolower($cfile) ;
if( basename($fil, '.php') !== 'qerror' )
{
	if( basename($fil, '.php') !== 'login' )
	{

	}
	if (!file_exists($cfile))
	{
		redirect('qerror/display/' . 404) ;
		return;
	}
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

if(function_exists('init_app') )
{
	init_app() ;
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
				$ret = array(
					'status' => 'FAIL',
					'message' => 'Access denied.',
				);
				if( ! @$_SESSION['usr_id'] )
				{
					$ret['__script'] = 'window.location="' . siteUrl('login') . '";' ;
				}
				
				jsonResponse($ret) ;
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
	
	$QFA['db']->setLogger(new Log()) ;

	if( $canCall )
	{
		call_user_func_array(array($o, $m), $a);
	}
	else
	{
		if( basename($fil, '.php') !== 'login' )
		{
			if( ! @$_SESSION['usr_id'] )
			{
				header('location:' . siteUrl('login')) ;
				return;	
			}
		}
	}
}
else
{
	trigger_error('Url not recognized.') ;
}
//DONE.
?>