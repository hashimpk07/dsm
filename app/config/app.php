<?php

/**
 * Default controller file name
 * Default method ( index() ) in this controller will be invoked
 * By default its home.php
 */

define('QC_LIMIT', 16 ) ;
define('QC_LIMIT_2X', 32 ) ;

define('QFS_DEFAULT_FILE', 'login.php');
define('QFS_ADMIN_EMAIL', 'nithin@alpha.qudratom.com');

define('QC_STR_ALL_LOCATIONS', 'All Branch') ;
define('QC_STR_OTHER_LOCATION', 'Other Branch') ;

define('QC_DATATYPE_INT', 1);
define('QC_SCREEN_UNIT', 'px');

define('QC_LANGUAGE_DEFAULT', '1'); //English

define('QC_STR_SELECT', 'Select');
define('QC_CONTENT_TYPE_I', 'I');
define('QC_CONTENT_TYPE_T', 'T');
define('QC_CONTENT_TYPE_H', 'H');
define('QC_CONTENT_TYPE_V', 'V');
define('QC_CONTENT_TYPE_VU', 'VU');
define('QC_CONTENT_TYPE_IU', 'IU');
define('QC_CONTENT_TYPE_S', 'S');

define('QC_DATATYPE_DECIMAL', 2);
define('QC_DATATYPE_STRING', 3);
define('QC_DATATYPE_BOOLEAN', 4);
define('QC_DATATYPE_DATE', 5);
define('QC_DATATYPE_TIME', 6);
define('QC_DATATYPE_DATETIME', 7);

define('QC_USR_SUPERADMIN','1');
define('QC_USR_HEAD_OP','2');
define('QC_USR_BRANCH_OP','3');

define('QC_DEPT_TYPE_NEW', 'N') ;
define('QC_DEPT_TYPE_JUNK', 'J') ;
define('QC_DEPT_TYPE_DEFAULT', 'D') ;

define('QC_ADMIN_UID', 1) ;
define('QC_ADMIN_GROUP', '1') ;

define('QC_ERR_INVALID_ARGS','Invalid argument(s)');

define('QC_STR_JUNK',1);
define('QC_STR_DISPOSED',2);
define('QC_STR_WRITEOFF',3);

/**
 * Enable database logging
 */
define('DB_LOG', true) ;


define('DS', DIRECTORY_SEPARATOR) ;
include dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'shared' . DS . 'helper' . DS . 'const.php' ;


function _loadLanguages()
{
	global $QFL_KEYS ;
	$mylangid = @QC_PRM_LANG_ID ;

	if( $mylangid)
	{
		$sql = "SELECT file FROM languages WHERE id='$mylangid' AND activated=1" ;
		global $QFA;
		$mylanguage = $QFA['db']->scalarField($sql) ;
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
}
function _loadParams()
{
	$sql = "SELECT code, value FROM parameter WHERE autoload=1" ;
	global $QFA;
	$set = $QFA['db']->fetchKV( $sql, 'code', 'value' ) ;
	foreach( $set as $k => $v )
	{
		if( ! defined($k) )
		{
			define($k, $v) ;
		}
	}
}
function init_app()
{
	_loadParams() ;
//	_loadLanguages() ;
}


global $APP_CONFIG;
/**
 * Error handler file.
 */
$APP_CONFIG['error_handler'] = 'qerror.php' ;

/**
 * Auto load helpers
 */
$APP_CONFIG['autoload_helper'] = array(
		 'crmgeneral.php' => array() /*, 'otherhelper.php' => array() */
		);

/**
 * Auto load models
 */
$APP_CONFIG['autoload_models'] = array(
		/* 'model1.php'  => array() , 'model2.php' => array() */
		);
/**
 * Auto load libraries
 */
$APP_CONFIG['autoload_library'] = array(
	'validations.php' => array(),
	'session.php' => array(), 
		);

