<?php

//set development mode
if (defined('QFS_DEVELOPMENT'))
{
	error_reporting(((QFS_DEVELOPMENT == 0 ) ? 0 : E_ALL & ~ E_STRICT));
}
session_start();

require_once 'jstatus.php';
require_once 'mvc.php';
require_once 'controller.php';
require_once 'layout.php';
require_once 'model.php';
require_once 'library.php';
require_once 'module.php';

/**
 * Database is connected automatically..
 */
require_once 'system/helper/essentials.php';
//require_once 'system/library/mysql.php';
require_once 'system/library/kmysql.php';
require_once 'system/library/input.php';
require_once 'system/library/log.php';

$apiapp		= $_REQUEST['app'] ;

global $QFA;
//Main connection {
$_dbconf_main = require_once ('app/' . 'config/database.php');

$QFA['dbconfmain'] = $_dbconf_main ;
$QFA['dbmain'] = new KMysql() ;
	$QFA['dbmain']->connect($_dbconf_main);
//}

//{Child connection
global $APP_CONFIG ;
$APP_CONFIG = require_once ( strtolower($apiapp) . '/' . 'config/app.php');
$_dbconf = require_once ( strtolower($apiapp) . '/' . 'config/database.php');

$QFA['dbconf'] = $_dbconf ;
$QFA['db'] = new KMysql() ;
	$QFA['db']->connect($_dbconf);
//}



$QFA['input'] = new input();

require_once 'router.api.php';
?>