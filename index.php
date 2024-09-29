<?php

@date_default_timezone_set('Asia/Calcutta') ;
define('TIMEZONE_DIFF_SECONDS', 19800) ;

$APPPATH = dirname(__FILE__) ;

ini_set('include_path', $APPPATH . '/system;' . $APPPATH . '/app/config;'. 
	$APPPATH . '/app/controllers;' . $APPPATH . '/app/views;' . 
	$APPPATH . '/shared/models;' .
	$APPPATH . '/system/library;');

/**
 * App directory name end with directory seperator
 */
define('QFS_APP', 'app/');

/**
 * Development stage
 * 
 * 0 : Live mode
 * 1 : In development, debug mode
 */
define('QFS_DEVELOPMENT', 1);

/**
 * This filename
 */
define('QFS_BOOT_FILE', basename(__FILE__));

include 'system/boot.php';
?>