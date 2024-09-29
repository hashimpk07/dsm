<?php

//set development mode
if (defined('QFS_DEVELOPMENT'))
{
	if( QFS_DEVELOPMENT == 1 )
	{
		ini_set('display_error', true) ;
	}
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

global $APP_CONFIG;
require_once (QFS_APP . 'config/app.php');

require_once (QFS_APP . 'config/database.php');
global $QFA;

$QFA['db'] = new KMysql() ;
	$QFA['db']->connect($QFA['dbconf']);

$QFA['input'] = new input();

require_once 'router.php';
?>