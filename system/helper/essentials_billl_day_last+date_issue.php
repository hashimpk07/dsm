<?php

/**
 * Get path of a file from root dir
 * 
 * @param string $file file to retrieve
 * @return string file path
 */
function baseUrl($file = null)
{
	$dirname = dirname($_SERVER['SCRIPT_NAME']);
	if ($dirname == '/')
	{
		$dirname = '';
	}

	return ($dirname . '/' . ltrim($file, '/')) ;
}

/**
 * Get path of a file in app directory
 * 
 * @param string $file file to retrieve
 * @return string file path
 */
function appUrl($file = null)
{
	$dirname = dirname($_SERVER['SCRIPT_NAME']);
	if ($dirname == '/')
	{
		$dirname = '';
	}
	return $dirname . '/' . QFS_APP . $file;
}

/**
 * Get a file path.
 * 
 * @param string $file filename
 * @return string file path.
 */
function fileUrl($file)
{
	return QFS_APP . $file;
}

/**
 * Get a path in site
 * 
 * @param string $path uri to resolve
 * @param bool $relative relative site url ( no http://hostname )
 * @return string url
 */
function siteUrl($path = null, $relative = true)
{
	$host = $_SERVER['SERVER_NAME'];
	$host .= '/';
	$dirname = dirname($_SERVER['SCRIPT_NAME']);
	if ($dirname == '/')
	{
		$dirname = '';
	}
	return ( (!$relative) ? 'http://' . $host : '') . $dirname . '/' . QFS_BOOT_FILE . '/' . $path;
}

/**
 * @author Codeigniter 
 * Detect uri from codeigniter.
 */
function _detect_uri()
{
	if (!isset($_SERVER['REQUEST_URI']) OR !isset($_SERVER['SCRIPT_NAME']))
	{
		return '';
	}

	$uri = $_SERVER['REQUEST_URI'];
	if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
	{
		$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
	}
	elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
	{
		$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
	}

	// This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
	// URI is found, and also fixes the QUERY_STRING server var and $_GET array.
	if (strncmp($uri, '?/', 2) === 0)
	{
		$uri = substr($uri, 2);
	}
	$parts = preg_split('#\?#i', $uri, 2);
	$uri = $parts[0];

	if ($uri == '/' || empty($uri))
	{
		return '/';
	}

	$uri = parse_url($uri, PHP_URL_PATH);

	// Do some final cleaning of the URI and return it
	return str_replace(array('//', '../'), '/', trim($uri, '/'));
}

/**
 * Get request url.
 */
function currentUrl()
{
	return _detect_uri();
}

/**
 * Redirect to specified url
 * 
 * @param string $url where to go
 */
function redirect($url)
{
	if (!preg_match('/^https?:/i', $url))
	{
		$url = siteUrl($url);
	}

	if (headers_sent())
	{
		echo '<script type="text/javascript">window.location.href="' . $url . '"; </script>';
	}
	else
	{
		header('location:' . $url);
	}
}

/**
 * Convert and send an array as json response.
 * 
 *
 * @param Array $array an array to output.
 */
function jsonResponse($array)
{
	header('content-type: application/json');
	echo json_encode($array);
}

function _a2x($array)
{
	$str = '';
	foreach ($array as $k => $v)
	{
		if (is_array($v))
		{
			$str .= "<$k>";
			$str .= _a2x($v);
			$str .= "</$k>";
		}
		else
		{
			$str .= "<$k>$v</$k>";
		}
	}
	return $str;
}

/**
 * Convert array to xml.
 * 
 * @param type $array entire array including root tag...
 */
function xmlResponse($array)
{
	header('content-type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo _a2x($array);
}

/**
 * File path.
 * 
 * @param string $path file path
 * @param string $filename file name
 */
function forceDownload($path, $filename = null, $contentType = 'application/octet-stream')
{
	if (!$filename)
	{
		$filename = basename($path);
	}

	if (file_exists($path))
	{
		header("Content-Type: $contentType");
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" . $filename . "\"");
		echo file_get_contents($path);
		return true;
	}
	return false;
}

/**
 * Get current date time.
 * @return string current date time as string
 */
function now($format = 'Y/m/d H:i:s')
{
	return date($format);
}

function clientDate($dt, $format = 'd-m-Y')
{
	return clientDateTime($dt, $format);
}
function clientTime($dt, $format = 'h:i a')
{
	return clientDateTime($dt, $format);
}

function clientDateTime($dt, $format = 'd-m-Y h:i a')
{
	if ($dt)
	{
		$t = strtotime($dt);
		if ($t && $t != -19800 && $dt != '0000-00-00' && $dt != '0000-00-00 00:00:00')
		{
			return Date($format, $t);
		}
	}
	return '';
}

function mysqlDate($dt)
{
	if ($dt)
	{
		return mysqlDateTime($dt, 'Y-m-d') ;
	}
	return '';
}
function mysqlDateTimeNow()
{
	return mysqlDateTime(Date('Y-m-d H:i:s')) ;
}
function mysqlDateNow()
{
	return mysqlDate(Date('Y-m-d')) ;
}
function mysqlDateTime($dt, $format = 'Y-m-d H:i:s')
{
	if ($dt)
	{
		return Date($format, strtotime($dt));
	}
	return '';
}

function findFile($file, $subPath, $lookupList = null)
{
	if ($subPath == 'modules')
	{
		$files = array(
			basename($file, '.php') . '/' . basename($file, '.php') . '.php',
			basename($file, '.php') . '.php'
				);
	}
	else
	{
		$files = array(basename($file, '.php') . '.php');
	}

	$paths = array();
	if (is_array($lookupList))
	{
		foreach ($lookupList as $v)
		{
			$paths[] = $v . '/' . $subPath;
		}
	}
	$paths[] = QFS_APP . $subPath;
	$paths[] = 'shared/' . $subPath;
	$paths[] = 'system/' . $subPath;

	$first = false;
	foreach ($paths as $path)
	{
		foreach ($files as $file)
		{
			$filepath = $path . '/' . $file;
			if (!$first)
			{
				$first = $filepath;
			}
			if (file_exists($filepath))
			{
				return $filepath;
			}
		}
	}
	return $first;
}

/**
 * Convert given string to selected language.
 * 
 * @global type $QFL_KEYS an array hold loaded lanauge conversions.
 * @param type $key string to convert
 * @return string converted language
 */
function l($key)
{
	global $QFL_KEYS;
	if (isset($QFL_KEYS[$key]))
	{
		return $QFL_KEYS[$key];
	}
	return $key;
}

function getSettings($module, $name)
{
	global $QFC;
	$qsettings = $QFC->loadLibrary('qsettings', array('module' => $module), true);
	$qsettings->minstance($module, QC_ADMIN_GROUP);
	return $qsettings->read($name, QC_ADMIN_UID);
}

function getAllSettings($module)
{
	global $QFC;
	$qsettings = $QFC->loadLibrary('qsettings', array('module' => $module), true);
	$qsettings->minstance($module, QC_ADMIN_GROUP);
	$records = $qsettings->readAll(QC_ADMIN_UID);
	$newset = array();
	foreach ($records as $rec)
	{
		$newset[$rec['us_param']] = $rec;
	}
	return $newset;
}

//function sendTemplateSms( $tplcode, $to, $tplvars )
//{
//	$SEND_URL = '' ;
//
//	global $QFC ;
//	$tplcode = strtolower($tplcode) ;
//	$sql = "SELECT sms FROM etemplates WHERE LOWER(code)='$tplcode';" ;
//	$values = $QFC->db->fetchRow($sql) ;
//	$sms = $values['sms'] ;
//
//	foreach( $tplvars as $k => $v )
//	{
//		$sms = str_ireplace($k, $v, $sms) ;
//	}
//	//send sms..
//	$outgoingUrl = $SEND_URL ;
//	$outgoingUrl = str_replace( '%to', $to, $outgoingUrl ) ;
//	$outgoingUrl = str_replace( '%message', urlencode($sms), $outgoingUrl ) ;
//
//	//curl
//	$ch = curl_init(); 
//	curl_setopt($ch, CURLOPT_URL, $outgoingUrl); 
//	//curl_setopt($ch, CURLOPT_HEADER, TRUE); 
//	curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
//	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); 
//	curl_exec($ch);
//	//OR
//	//file_get_contents($outgoingUrl) ;
//	return true ;
//}
//function sendTemplateMail( $tplcode, $to, $tplvars, $from = null, $config = null )
//{
//	global $QFC ;
//	$QFC->loadLibrary('phpmailer.php') ;
//	$mail = new PHPMailer(true) ;
//
//	$tplcode = strtolower($tplcode) ;
//	$sql = "SELECT subject, message FROM etemplates WHERE LOWER(code)='$tplcode';" ;
//	$values = $QFC->db->fetchRow($sql) ;
//	$subject = $values['subject'] ;
//	$message = $values['message'] ;
//
//	$mail->AddAddress($to) ;
//
//	foreach( $tplvars as $k => $v )
//	{
//		$subject = str_ireplace($k, $v, $subject) ;
//		$message = str_ireplace($k, $v, $message) ;
//	}
//	$mail->SetFrom( (($from) ? $from : QFS_ADMIN_EMAIL) ) ;
//	$mail->AddReplyTo( (($from) ? $from : QFS_ADMIN_EMAIL) ) ;
//	if(is_array($config) && @$config['host'] )
//	{
//		$mail->IsSMTP();
//		$mail->Host       = $config['host'] ;
//		$mail->SMTPDebug  = 2 ;
//		$mail->SMTPAuth   = true ;
//		$mail->Port       = $config['port'] ;
//		$mail->Username   = $config['username'] ;
//		$mail->Password   = $config['password'] ;
//	}
//
//	$mail->Subject = $subject ;
//	$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!' ;
//	$mail->MsgHTML( $message ) ;
//	try
//	{
//		$mail->Send() ;
//		$mail->ClearAddresses() ;
//	}
//	catch(Exception $e)
//	{
//		return false ;
//	}
//	
//	return true ;
//}
function dateOrDayField($value, $format = 'Y-m-d H:i:s', $from = null)
{
	$value = trim($value);
	if ($value)
	{
		//is date ?
		if ((stripos($value, '/') !== false ) || (stripos($value, '-') !== false ))
		{
			return Date($format, strtotime($value));
		}
		//no of days ??
		$start = time();
		if ($from)
		{
			$start = strtotime($from);
		}
		return Date($format, ($start + (intval($value) * 24 * 60 * 60)));
	}
	return false;
}

function fixDate($inputDate)
{
	$date = str_replace('/', '-', $inputDate);
	return str_replace('\\', '-', $date);
}

function getUserParam($name)
{
	global $QFC;
	$usrId = $QFC->session->get('usr_id');
	$sql = "SELECT us_value FROM user_settings WHERE us_emp_id='$usrId' AND us_param='$name' LIMIT 1";
	return $QFC->db->scalarField($sql);
}

function getParam($name)
{
	global $QFC;
	$usrId = QC_ADMIN_UID;
	$sql = "SELECT us_value FROM user_settings WHERE us_emp_id='$usrId' AND us_param='$name' LIMIT 1";
	return $QFC->db->scalarField($sql);
}

function printCustomerAddress($rec, $sep = ", \n")
{
	$address = '';
	if (@($rec['customer']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= $rec['customer'];
	}
	else if (@($rec['cus_name']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= $rec['cus_name'];
	}
	if (@($rec['addr_line']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= $rec['addr_line'];
	}
	if (@($rec['addr_street']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= $rec['addr_street'];
	}
	if (@($rec['addr_city']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= $rec['addr_city'];
	}
	if (@($rec['st_name']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= $rec['st_name'];
	}
	if (@($rec['cn_name']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= $rec['cn_name'];
	}
	if (@($rec['add_zip']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= 'Pin : ' . $rec['add_zip'];
	}
//	$phone = '' ;
//	if( @$rec['cus_phmob'] )
//	{
//		$phone = $rec['cus_phmob'] ;
//	}
//	else if( @$rec['cus_phoffice'] )
//	{
//		$phone = $rec['cus_phoffice'] ;
//	}
//	else if( @$rec['cus_phres'] )
//	{
//		$phone = $rec['cus_phres'] ;
//	}
//	if( $phone )
//	{
//		if( $address )
//		{
//			$address .= $sep ;
//		}
//		$address .= 'Ph: ' . $phone ;
//	}
	return $address;
}

function printCustomerAddress2($rec, $sep = ", \n")
{
	$sep = '';
	$address = '';
	if (@($rec['customer']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= "<p>" . $rec['customer'] . "</p>";
	}
	else if (@($rec['cus_name']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= "<p>" . $rec['cus_name'] . "</p>";
	}
	if (@($rec['cus_house']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= "<p>" . $rec['cus_house'] . "</p>";
	}
	if (@($rec['cus_location']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= "<p>" . $rec['cus_location'] . "</p>";
	}
	if (@($rec['cus_postoffice']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= "<p>" . $rec['cus_postoffice'] . "</p>";
	}
	if (@($rec['cus_district']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= "<p>" . $rec['cus_district'] . "</p>";
	}
	if (@($rec['cus_pin']))
	{
		if ($address)
		{
			$address .= $sep;
		}
		$address .= "<p>" . 'Pin : ' . $rec['cus_pin'] . "</p>";
	}
//	$phone = '' ;
//	if( @$rec['cus_phmob'] )
//	{
//		$phone = $rec['cus_phmob'] ;
//	}
//	else if( @$rec['cus_phoffice'] )
//	{
//		$phone = $rec['cus_phoffice'] ;
//	}
//	else if( @$rec['cus_phres'] )
//	{
//		$phone = $rec['cus_phres'] ;
//	}
//	if( $phone )
//	{
//		if( $address )
//		{
//			$address .= $sep ;
//		}
//		$address .= 'Ph: ' . $phone ;
//	}
	return $address;
}

function sendEmail($subject, $msg, $records, $field, &$yes_email, &$no_email)
{
	$from = getUserParam('email');
	$no_email = 0;
	$yes_email = 0;
	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= "From: $from" . "\r\n";

	$_tmpSubject = $subject;

	foreach ($records as $rec)
	{
		if (!$rec[$field])
		{
			$no_email++;
			continue;
		}
		$_tmpMsg = $msg;
		$tostr = $rec['email'];
		if (!$tostr)
		{
			continue;
		}

		$yes_email++;

		//replace personalize parameter variables..
		foreach ($rec as $k => $v)
		{
			$_tmpMsg = str_ireplace('{' . $k . '}', $v, $_tmpMsg);
		}
		mail($tostr, $_tmpSubject, $_tmpMsg, $headers);
	}
}

function mailCompactible($data1, $data2)
{
	$str = '';
	if (is_array($data2))
	{
		foreach ($data2 as $k => $v)
		{
			if ($str)
			{
				$str .= '&';
			}
			$str .= base64_encode($k) . '=' . base64_encode($v);
		}
	}
	if (is_array($data1))
	{
		foreach ($data1 as $k => $v)
		{
			if ($str)
			{
				$str .= '&';
			}
			$str .= $k . '=' . urlencode(base64_encode($v));
		}
	}
	$url = siteUrl('ajax/mail?' . $str);
	return ("<a href='$url'>If you have problem viewing this page, click here to view this mail in seperate page.</a>");
}

function sendSmtpEmail($subject, $msg, $records, $field, &$yes_email, &$no_email, $tpla = null)
{
	global $QFC;
	$QFC->loadLibrary('phpmailer.php');
	$mail = new PHPMailer(true);

	//get smtp details
	$params = getAllSettings('communication');
	$from = '';
	if (@$params['email']['us_value'])
	{
		$from = $params['email']['us_value'];
	}
	if (!$from)
	{
		$from = $params['email_id']['us_value'];
	}
	$no_email = 0;
	$yes_email = 0;
	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= "From: $from" . "\r\n";

	$_tmpSubject = $subject;

	foreach ($records as $rec)
	{
		if (!$rec[$field])
		{
			$no_email++;
			continue;
		}
		$_tmpMsg = $msg;
		$tostr = $rec['email'];
		if (!$tostr)
		{
			$no_email++;
			continue;
		}

		$yes_email++;

		//replace personalize parameter variables..
		foreach ($rec as $k => $v)
		{
			$_tmpMsg = str_ireplace('{' . $k . '}', $v, $_tmpMsg);
		}

		$mail->ClearAddresses();
		
		$mail->setFrom($from);
		$mail->AddReplyTo($from);
		$mail->IsSMTP();
		$mail->Host = $params['email_host']['us_value'];
		$mail->SMTPDebug = 2;
		$mail->SMTPAuth = true;
		$mail->Port = $params['email_port']['us_value'];
		$mail->Username = $params['email_user']['us_value'];
		$mail->Password = $params['email_pass']['us_value'];
		$mail->SetFrom($from);
		$mail->AddReplyTo($from);

		$mail->AddAddress($rec['email'], $rec['customer']);
		$mail->Subject = $_tmpSubject;
		$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
		$_tmpMsg = stripslashes($_tmpMsg);
		$mail->MsgHTML($_tmpMsg);
		$mail->Send();
		
		$sql = "INSERT INTO messagelog(type, message, extra, dt) VALUES('EMAIL', '$_tmpMsg', 'Sub:$_tmpSubject<br/>To:$rec[email]', NOW());" ;
		global $QFC ;
		$QFC->db->execute($sql) ;
	}
}

function sendSms($msg, $records, $field, $sender, &$yes_sms, &$no_sms)
{
	if (!$sender)
	{
		$list = explode(',', getParam('sms_sender'));
		if (is_array($list))
		{
			$sender = reset($list);
		}
	}
	$outgoingUrl = 'http://alerts.smsclogin.com/api/web2sms.php?workingkey=%workingkey&sender=%sender&to=%to&message=%message';
	$workingkey = getParam('sms_api');
	$sender = $sender;

	$no_sms = 0;
	$yes_sms = 0;

	foreach ($records as $rec)
	{
		if (!$rec[$field])
		{
			$no_sms++;
			continue;
		}
		$_tmpUrl = $outgoingUrl;
		$_tmpMsg = $msg;

		$tostr = $rec['mobile'];
		if (@$rec['spouse'] == 'spouse')
		{
			if ($tostr && @$rec['cus_spouse_mobile'])
			{
				$tostr .= ',';
			}
			$tostr .= $rec['cus_spouse_mobile'];
		}

		if (!$tostr)
		{
			continue;
		}
		$yes_sms++;

		//replace personalize parameter variables..
		foreach ($rec as $k => $v)
		{
			$_tmpMsg = str_ireplace('{' . $k . '}', $v, $_tmpMsg);
		}
		$_tmpMsg = urlencode($_tmpMsg);

		//create url
		$_tmpUrl = str_ireplace('%to', $tostr, $_tmpUrl);
		$_tmpUrl = str_ireplace('%message', $_tmpMsg, $_tmpUrl);
		$_tmpUrl = str_ireplace('%sender', $sender, $_tmpUrl);
		$_tmpUrl = str_ireplace('%workingkey', $workingkey, $_tmpUrl);

		//outgoing...
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $_tmpUrl);
		//curl_setopt($ch, CURLOPT_HEADER, TRUE); 
		curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_exec($ch);
		//
		$sql = "INSERT INTO messagelog(type, message, extra, dt) VALUES('SMS', '$_tmpMsg', '$tostr', NOW());" ;
		global $QFC ;
		$QFC->db->execute($sql) ;
		//OR
		//file_get_contents($outgoingUrl) ;
	}
}

function sendTemplateSms($usrid, $template, $customParams = null)
{
	global $QFC;
	$sqlbday = "SELECT * FROM templates WHERE tpl_name='$template' AND tpl_type='S'";
	$tpl = $QFC->db->fetchRow($sqlbday);

	$dt = Date('d-m-Y');
	$sql = "SELECT cus_subscribe_sms as bsms, cus_subscribe_email as bemail, cus_phmob as mobile, cus_email as email, cus_name as customer, '$dt' as `date`, cus_birthday as birthday, cus_anniversary as anniversary, cus_organization as organization, cus_designation as designation,
			concat( if( length(cus_house) > 0, concat( cus_house, ',' ) , '' ) , if( length(cus_location) > 0, concat( cus_location, ',' ) , '' ) , if( length(cus_postoffice) > 0, concat( cus_postoffice, ',' ) , '' ) , if( length(cus_district) > 0, concat( cus_district, ',' ) , '' ) , if( length(cus_pin) > 0, concat( cus_pin, ',' ) , '' ) ) as address
			FROM customers WHERE cus_deleted=0 AND cus_id='$usrid'";

	$cusregister = $QFC->db->fetchRowSet($sql);

	if (is_array($customParams))
	{
		foreach ($customParams as $k => $v)
		{
			$tpl['tpl_message'] = str_ireplace($k, $v, $tpl['tpl_message']);
		}
	}
	sendSms($tpl['tpl_message'], $cusregister, 'bsms', null, $yes_sms, $no_sms);
}

function sendTemplateEmail($usrid, $template, $customParams = null)
{
	global $QFC;
	$sqlbday = "SELECT * FROM templates WHERE tpl_name='$template' AND tpl_type='e'";
	$tpl = $QFC->db->fetchRow($sqlbday);

	$dt = Date('d-m-Y');
	$sql = "SELECT cus_subscribe_sms as bsms, cus_subscribe_email as bemail, cus_phmob as mobile, cus_email as email, cus_name as customer, '$dt' as `date`, cus_birthday as birthday, cus_anniversary as anniversary, cus_organization as organization, cus_designation as designation,
		concat( if( length(cus_house) > 0, concat( cus_house, ',' ) , '' ) , if( length(cus_location) > 0, concat( cus_location, ',' ) , '' ) , if( length(cus_postoffice) > 0, concat( cus_postoffice, ',' ) , '' ) , if( length(cus_district) > 0, concat( cus_district, ',' ) , '' ) , if( length(cus_pin) > 0, concat( cus_pin, ',' ) , '' ) ) as address
		FROM customers WHERE cus_deleted=0 AND cus_id='$usrid'";
	$cusinfo = $QFC->db->fetchRowSet($sql, 'assoc');

	if (is_array($customParams))
	{
		foreach ($customParams as $k => $v)
		{
			$tpl['tpl_message'] = str_ireplace($k, $v, $tpl['tpl_message']);
		}
	}
	//unscribe link {
	foreach( $cusinfo as $k => $v )
	{
		$email = urlencode( base64_encode(strtolower($v['email'])) ) ;
		$hash = md5( (strtolower($v['email']) . 'qudratom.bcrm') ) ;

		$u = '<a href="' . siteUrl("unsubscribe/u?hash=$hash&e=$email", false) . '">unsubscribe</a>' ;

		$cusinfo[$k]['unsubscribe'] = $u ;
	}
	//} unsubscribe link
	sendSmtpEmail($tpl['tpl_subject'], $tpl['tpl_message'], $cusinfo, 'bemail', $yes_sms, $no_sms, $customParams);
}
function drawFirst($title)
{
	return $title ;
}
function drawTh($title, $column)
{
	if( ! $column )
	{
		return $title;
	}
    $sort = 'asc';

	if (@$_REQUEST['searchq-sort'] == 'asc' && $_REQUEST['searchq-col'] == $column)
	{
		$sort = 'desc';
	}
	global $QFC;
    $url = $QFC->vars['pager_url'] ;
	$join = '?' ;
	if(stripos($url, '?') !== false)
	{
		$join = '&' ;
	}
	$str = '';
	$loclist = $QFC->input->request;

	unset($loclist['searchq-col']);
	unset($loclist['searchq-sort']);

	foreach ($loclist as $k => $v)
	{
		if (stripos($k, 'search') === 0)
		{
			if ($str)
			{
				$str .= '&';
			}
			$str .= "hid-$k=$v";
		}
		else if (stripos($k, 'hid-search') === 0)
		{
			if ($str)
			{
				$str .= '&';
			}
			$str .= "$k=$v";
		}
	}
    $listArea = 'idListArea' . get_class($QFC) ;
	return "<a href='javascript:void(0)' onclick='getData(\"$url" . "{$join}searchq-col=$column&searchq-sort=$sort&$str\", null,\"$listArea\", null, null);' >$title</a>";
}

function array2Csv($recset, $columns, $header = null)
{
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename=excel' . Date('YmdHis') . '.csv');

	$handle = fopen('php://output', 'w');

	if (is_array($header))
	{
		foreach ($header as $v)
		{
			fputcsv($handle, $v);
		}
	}

    if( ! is_array($columns) )
    {
        $columnsNew = array() ;
        $columnsTemp = array_keys(reset($recset)) ;
        foreach( $columnsTemp as $k => $v )
        {
            $key = $v ;
            $s = stripos($v, '_') ;
            if( $s !== false )
            {
                $s ++ ;
            }
            $v = substr($v, $s) ;
            $v = ucfirst($v) ;
            $columnsNew[$key] = $v ;
        }

        $columns = $columnsNew ;
    }
	fputcsv($handle, $columns);

	if (is_resource($recset))
	{
		while ($trec = mysql_fetch_array($recset))
		{
			$rec = array();
			foreach ($columns as $k => $v)
			{
				$rec[] = @$trec[$k];
			}
			fputcsv($handle, $rec);
		}
	}
	else
	{
		foreach ($recset as $row)
		{
			$rec = array();
			foreach ($columns as $k => $v)
			{
				$rec[] = @$row[$k];
			}
			fputcsv($handle, $rec);
		}
	}
	fclose($handle);
}
function masterEntry($entry, $table, $id_col, $value_col, $del_flag, &$exists=false)
{
	if (!$entry)
	{
		return false;
	}
	
	global $QFC ;
	
	$sql = "SELECT $id_col FROM $table WHERE $value_col='$entry' AND $del_flag=0";
	$id = $QFC->db->scalarField($sql);
	if ($id)
	{
		$exists = true ;
		return $id;
	}
	else
	{
		$sql = "INSERT INTO $table($value_col) VALUES('$entry')";
		$QFC->db->execute($sql);
		return $QFC->db->getLastInsertId();
	}
	$exists = null ;
	return false;
}
/**
 * Count non empty/false values in an array.
 * 
 * @param type $array
 */
		
function countReal($array)
{
	$i = 0 ;
	foreach( $array as $v )
	{
		if( $v )
		{
			$i ++ ;
		}
	}
	return $i ;
}

function getTableName($filename)
{
	return str_ireplace('_model', '', strtolower($filename));
}
function cls($data)
{
	if( ! $data )
	{
		return 'hideit' ;
	}
}
function isAdmin()
{
	global $QFC ;
	if( $QFC->session->get('usr_grp_code') == 'S' )
	{
		return true ;
	}
	return false ;
}
function isOtherData($module)
{
	if(isAdmin() )
	{
		return true ;
	}
	global $QFC ;
	$OTHERS = 5 ;
	$empId = $QFC->session->get('usr_id') ;
	$sqlp = "SELECT * FROM privileges p"
			. "	INNER JOIN employee_privileges ep ON ep.ep_priv_id=p.priv_id WHERE ep.ep_emp_id='$empId' AND p.priv_sort_action='$OTHERS'" ;
	$records = $QFC->db->fetchRowSet($sqlp) ;

	foreach( $records as $rec )
	{
		if( $rec['priv_sort_module'] == $module )
		{
			return true ;
		}
	}
	
	if( $QFC->session->get('emp_otherdata') )
	{
		return true ;
	}
	return false ;
}
/**
 * Prevent single quoting a value.
 * 
 * @param string $value value to prevent quote
 * @return string a system specific protected string which prvent quotes for system functions. eg: NULL, NOW() etc..
 */
function sqlNoQuote($value)
{
 return QFS_SQLNOQUOTE_TOKEN . $value ;
}
/**
 * Make a string null compatible.
 * 
 * @param string $value value to check and make null if 0 or '' is given.
 * @return string return null value if key value is null string or 0.
 */
function sqlNullableKey($value)
{
 if( ! $value )
 {
  return sqlNoQuote('NULL') ;
 }
 return $value ;
}
/**
 * Make a string null compatible.
 * 
 * @param string $value value to check and make null if 0 or '' is given.
 * @return string return null value if key value is null string or 0.
 */
function sqlNullableKeyString($value)
{
 if( ! $value )
 {
  return sqlNoQuote('NULL') ;
 }
 return "'" . $value . "'" ;
}

/**
 * Generate uniq random string.
 * 
 * @return string unique string
 */
function getUniqId()
{
	return md5(uniqid(rand(), true) . rand()) ;
}

/**
 * Get controller information value from db.
 * 
 * @global type $QFA gloabl controller info array.
 * @param type $attribute attribute name if any
 * @return string|array value on success, if $attribute is null it will return entrie attribute collection as array.
 */
function getControllerInfo($attribute = null)
{
	global $QFA ;
	if( $attribute )
	{
		if( isset($QFA['_ci'][$attribute]) )
		{
			return $QFA['_ci'][$attribute] ;
		}
		else
		{
			return false ;
		}
	}
	return $QFA['_ci'] ;
}
/**
 * Relate parent / child.
 * 
 * @credits Nate Weiner
 * @param array $array array to filter, expecting associative array.
 * 
 * Expected format
 * ---------------
 * array( 
 *   array( item_id :  
 *			parent_id : 
 *			name
 *		  ),
 * )
 */
function onePassAdjacencyTree($array, $colMap = null )
{
	$ITEM_ID	= 'item_id' ;
	$PARENT_ID	= 'parent_id' ;
	$NAME		= 'name' ;
	$CHILDREN	= 'children' ;
	
	if( is_array($colMap) )
	{
		if( isset($colMap['item_id']) )
		{
			$ITEM_ID = $colMap['item_id'] ;
		}
		if( isset($colMap['parent_id']) )
		{
			$PARENT_ID = $colMap['parent_id'] ;
		}
		if( isset($colMap['name']) )
		{
			$NAME = $colMap['name'] ;
		}
		if( isset($colMap['children']) )
		{
			$CHILDREN = $colMap['children'] ;
		}
	}
	$refs = array();
	$list = array();

	foreach( $array as $data )
	{
		$thisref = &$refs[ $data[$ITEM_ID] ];

		$thisref[$PARENT_ID] = $data[$PARENT_ID];
		$thisref[$NAME] = $data[$NAME];

		if ($data[$PARENT_ID] == 0) {
			$list[ $data[$ITEM_ID] ] = &$thisref;
		} else {
			$refs[ $data[$PARENT_ID] ]['children'][ $data[$ITEM_ID] ] = &$thisref;
		}
	}

	return $list ;
}

function formatNumber($number, $points = 2)
{
	return number_format($number, $points, '.', '') ;
}



	function calculatedBillDetails($billId, $ret = false)
	{
		global $QFC ;
		$sql = "SELECT * FROM bill WHERE bl_id='$billId'" ;
		//get basic bill info
		$bi = $QFC->db->fetchRow($sql) ;
		$data = array('idBlAmount' => 0, 'idBlDueDt' => 0, 'idBlFine' => 0) ;
		if(is_array($bi) )
		{
			$data['idBlAmount'] = formatNumber( $bi['bl_amount'] ) ;
			$data['idBlDueDt'] = clientDate($bi['bl_due_dt']) ;
			$data['idBlFine'] = formatNumber( calculateFine($bi['bl_id']) ) ;
		}

		//get paid amount
		$sqlp = "SELECT SUM(bp_amount) as tot_paid FROM bill_payment WHERE bp_bill_id='$billId' AND bp_deleted=0 " ;
		$data['idBlPrevTotal'] = formatNumber( $QFC->db->scalarField($sqlp) ) ;
		$finalAmount = ( ($data['idBlAmount'] - $data['idBlPrevTotal']) > 0 ? ($data['idBlAmount'] - $data['idBlPrevTotal']) : 0 ) + $data['idBlFine'] ;
		$data['idBlTotal'] = formatNumber( $finalAmount ) ;
		$data['net_amount'] = doubleval( $finalAmount ) ;

		if( $ret )
		{
			return $data ;
		}
		jsonResponse($data) ;
	}
	function diffDays($from, $to)
	{
		return ( ($to - $from) / 86400 ) ;
	}
	function calculateFine($billId)
	{
		global $QFC ;

		$sqlb = "SELECT * FROM bill WHERE bl_id='$billId' " ;
		$bill = $QFC->db->fetchRow($sqlb) ;
		$sqlp = "SELECT * FROM bill_payment WHERE bp_bill_id='$billId' AND bp_deleted='0' ORDER BY bp_dt ASC" ;
		$bill_payment = $QFC->db->fetchRowSet($sqlp) ;

		$_totAmount = $bill['bl_amount'] ;
		$_dueTs = strtotime( $bill['bl_due_dt'] ) ;
		$_lastTs = $_dueTs ;

		$_slab = QC_PROP_SLAB_DAYS ;
		$_beforeSlab = QC_PROP_BEFORE_SLAB ;
		$_afterSlab = QC_PROP_AFTER_SLAB ;
		$_paidUpto = 0 ;
		$_prevTs = $_dueTs ;
		$_fineTotal = 0 ;

		$bill_payment[] = array(
			'bp_dt' => Date('Y-m-d H:i:s'),
			'bp_amount' => 0
		) ;

		foreach( $bill_payment as $payment )
		{
			$_lastTs = strtotime($payment['bp_dt']) ;
			$_slabTs = $_dueTs + (86400 * $_slab) ;

			if( $_lastTs < $_dueTs )
			{
				continue ;
			}

			$_diffDays = floor(diffDays( $_prevTs, $_lastTs )) ;
			$_diffDaysSlabAfter = ( ( $_lastTs > $_slabTs ) ? diffDays( $_slabTs, $_lastTs ) : 0 ) ;
			if( $_diffDaysSlabAfter > $_diffDays )
			{
				$_diffDaysSlabAfter = $_diffDays ;
			}
			$_diffDaysSlabBefore = $_diffDays - $_diffDaysSlabAfter ;
			if( ($_totAmount - $_paidUpto) > 0 )
			{
				if( $_diffDaysSlabBefore > 0 )
				{
					$_fineTotal += ( (($_totAmount - $_paidUpto) * $_beforeSlab) / 100 ) * $_diffDaysSlabBefore ;
				}
				if( $_diffDaysSlabAfter > 0 )
				{
					$_fineTotal += ( (($_totAmount - $_paidUpto) * $_afterSlab) / 100 ) * $_diffDaysSlabAfter ;
				}
			}

			//Last Lines {
			$_paidUpto = $_paidUpto + $payment['bp_amount'] ;
			$_prevTs = $_lastTs ;
			//} 
		}
		if( $_paidUpto > $_totAmount )
		{
			$_fineTotal -= ($_paidUpto - $_totAmount );
		}
		if( $_fineTotal < 0 )
		{
			$_fineTotal = 0 ;
		}
		return $_fineTotal ;
	}

/**
 * trims text to a space then adds ellipses if desired
 * @param string $input text to trim
 * @param int $length in characters to trim to
 * @param bool $ellipses if ellipses (...) are to be added
 * @param bool $strip_html if html tags are to be stripped
 * @return string
 */
function wordStrip($input, $length, $ellipses = true, $strip_html = true) {
    //strip tags, if desired
    if ($strip_html) {
        $input = strip_tags($input);
    }
  
    //no need to trim, already shorter than trim length
    if (strlen($input) <= $length) {
        return $input;
    }
  
    //find last space within length
    $last_space = strrpos(substr($input, 0, $length), ' ');
    $trimmed_text = substr($input, 0, $last_space);
  
    //add ellipses (...)
    if ($ellipses) {
        $trimmed_text .= '...';
    }
  
    return $trimmed_text;
}
function preBill($dt = null)
{
	$bill_dt = " CURDATE() " ;
	if( $dt )
	{
		$bill_dt = " '" . Date('Y-m-d', strtotime($dt)) . "' " ;
	}

	$sql = "SELECT af_id, 
					IF( DATE_ADD(af_last_dt, INTERVAL af_bill_cycle MONTH) > af_end_dt, true, false) as b_partial_m, 
					IF( DATE_ADD(af_last_dt, INTERVAL af_bill_cycle DAY) > af_end_dt, true, false) as b_partial_d, 
					TIMESTAMPDIFF( DAY, af_last_dt, af_end_dt) as days_to_end,
					TIMESTAMPDIFF( HOUR, af_last_dt, af_end_dt) as hours_to_end,
					af_bill_cycle as days_to_next_db,
					TIMESTAMPDIFF( DAY, af_last_dt, DATE_ADD(af_last_dt, INTERVAL af_bill_cycle MONTH) ) as days_to_next_mb,
					TIMESTAMPDIFF( HOUR, af_last_dt, DATE_ADD(af_last_dt, INTERVAL af_bill_cycle DAY) ) as hours_to_next_db,
					TIMESTAMPDIFF( HOUR, af_last_dt, DATE_ADD(af_last_dt, INTERVAL af_bill_cycle MONTH) ) as hours_to_next_mb,
					
					DATE_ADD(af_last_dt, INTERVAL af_bill_cycle MONTH) as next_month,
					DATE_ADD(af_last_dt, INTERVAL af_bill_cycle DAY) as next_day,
				af_ag_id, ag_cus_id, af_feat_id, af_feat_type, af_start_dt, af_end_dt, af_last_dt, UPPER(af_unit) as af_unit, af_unit_price, af_bill_cycle, UPPER(af_bill_cycle_unit) as af_bill_cycle_unit, ru_id, ru_old_value, ru_new_value
			FROM agreement_feature AS af
			INNER JOIN agreement a ON a.ag_id=af.af_ag_id
			LEFT JOIN rawusage r ON r.ru_af_id=af.af_id
			WHERE DATE( af_last_dt ) = $bill_dt AND UPPER(af_bill_schedule) = 'P' AND af_closed = 0 ;" ;

		global $QFC ;
		$QFC->db->beginTrans() ;
		$recset = $QFC->db->fetchRowSet($sql, 'assoc') ;
		foreach( $recset as $rec )
		{
			$next_dt = null ;
			if( $rec['af_bill_cycle_unit'] == 'M' )
			{
				$next_dt = $rec['next_month'] ;
				$hours_to_next = $rec['hours_to_next_mb'] ;
				$days_to_next = $rec['days_to_next_mb'] ;
			}
			else if( $rec['af_bill_cycle_unit'] == 'D' )
			{
				$next_dt = $rec['next_day'] ;
				$hours_to_next = $rec['hours_to_next_db'] ;
				$days_to_next = $rec['days_to_next_db'] ;
			}
			if( ($rec['b_partial_m'] && $rec['af_bill_cycle_unit'] == 'M') || ($rec['b_partial_d'] && $rec['af_bill_cycle_unit'] == 'D') )
			{
				//if partial use agreement end dt..
				$next_dt = $rec['af_end_dt'] ;
				
				$hours = $rec['hours_to_end'] ;
				$days = $rec['days_to_end'] ;
			}
			else
			{
				$hours = $hours_to_next ;
				$days = $days_to_next ;
			}

			$days_in_month = cal_days_in_month(CAL_GREGORIAN, Date('m', strtotime($rec['af_last_dt'])) , Date('Y', strtotime($rec['af_last_dt']))) ;
			$days_in_year = (Date('L', strtotime($rec['af_last_dt'])) ? 366 : 365) ;
			switch( $rec['af_unit'] )
			{
				case 'H' :
					$vdBillAmount = $rec['af_unit_price'] * $hours ;
					break ;
				case 'D' :
					$vdBillAmount = $rec['af_unit_price'] * $days ;
					break ;
				case 'M' :
					$vdBillAmount = ( ($rec['af_unit_price'] * $rec['af_bill_cycle']) / $days_to_next) * $days ; 
					break ;
				case 'Y' :
					$vdBillAmount = ($rec['af_unit_price'] / $days_in_year) * $days ; 
					break ;
				case 'U' :
					$vdUsage = $rec['ru_new_value'] - $rec['ru_old_value'] ;
					$vdBillAmount = $vdUsage * $rec['af_unit_price'] ;
					$sqlu = "UPDATE rawusage SET ru_new_value = ru_old_value, ru_new_value=0 WHERE ru_id = '$rec[ru_id]' " ;
					$QFC->db->execute($sqlu) ;
					break ;
			}
			if( $rec['af_unit'] != 'U' )
			{
				$vdUsage = 0 ;
			}
			//only if there are some amount
			if( $vdBillAmount )
			{
				$sqli = "INSERT INTO rawbilling( rb_ag_id, rb_cus_id, rb_feat_id, rb_feat_type, rb_dts, rb_dte, rb_unit, 
									rb_unit_price, rb_usage, rb_bill_amount, rb_deposit, rb_billed) VALUES
								('$rec[af_ag_id]', '$rec[ag_cus_id]', '$rec[af_feat_id]', '$rec[af_feat_type]', '$rec[af_last_dt]', '$next_dt', '$rec[af_unit]', 
									'$rec[af_unit_price]', '$vdUsage', '$vdBillAmount', '0', '0') ;" ;
				$QFC->db->execute($sqli) ;
			}

			//update last dt 
			$nextDtStr = Date('Y-m-d H:i:s', strtotime($next_dt)) ;
			$sqlul = "UPDATE agreement_feature SET af_last_dt='$nextDtStr' WHERE af_id=$rec[af_id] LIMIT 1" ;
			$QFC->db->execute($sqlul) ;
		}
		$QFC->db->commitTrans() ;
}

function _billAmountAcrossMonth($from, $to, $unitPrice )
{
	$vdBillAmount = 0 ;
	$obj = new QDate() ;

	$end_ts = strtotime($to) ;
	do
	{
		$next_ts = strtotime($from) ;

		//only bill cycle is monthly...
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, Date('m', strtotime($next_ts)) , Date('Y', strtotime($next_ts))) ;
		if( $end_ts < strtotime( Date('Y-m-' . $days_in_month, time()) ) )
		{
			$days_in_month = Date('d', strtotime($end_ts)) ;
		}

		$start_of_month = Date('d', strtotime($next_ts)) ;
		$start_of_month -- ;
		$days_usable = ($days_in_month - $start_of_month) ;

		$vdBillAmount += ($unitPrice / $days_in_month) * $days_usable ;

		$obj->initialize($next_ts) ;
		$next_ts = $obj->addMonths(1);

	}while( $next_ts < $end_ts ) ;
}

function postBill($dt = null)
{
	$bill_dt = " DATE_SUB(CURDATE(), INTERVAL 1 DAY) " ;
	if( $dt )
	{
		$ts = strtotime($dt) - 86400 ;
		$bill_dt = " '" . Date('Y-m-d', $ts)  . "' " ;
	}
	
	$af_end_dt_plus_one = ' DATEADD(af_end_dt, INTERVAL 1 DAY) ' ;

	$sql = "SELECT af_id, 
					IF( DATE_ADD(af_last_dt, INTERVAL af_bill_cycle MONTH) > af_end_dt, true, false) as b_partial_m, 
					IF( DATE_ADD(af_last_dt, INTERVAL af_bill_cycle DAY) > af_end_dt, true, false) as b_partial_d, 
					TIMESTAMPDIFF( DAY, af_last_dt, af_end_dt) as days_to_end,
					TIMESTAMPDIFF( HOUR, af_last_dt, af_end_dt) as hours_to_end,
					
					TIMESTAMPDIFF( HOUR, af_last_dt, $bill_dt ) as hours_to_cur,
					TIMESTAMPDIFF( DAY, af_last_dt, $bill_dt ) as days_to_cur,

					af_bill_cycle as days_to_next_db,
					TIMESTAMPDIFF( DAY, af_last_dt, DATE_ADD(af_last_dt, INTERVAL af_bill_cycle MONTH) ) as days_to_next_mb,
					TIMESTAMPDIFF( HOUR, af_last_dt, DATE_ADD(af_last_dt, INTERVAL af_bill_cycle DAY) ) as hours_to_next_db,
					TIMESTAMPDIFF( HOUR, af_last_dt, DATE_ADD(af_last_dt, INTERVAL af_bill_cycle MONTH) ) as hours_to_next_mb,
					
					DATE_ADD(af_last_dt, INTERVAL af_bill_cycle MONTH) as next_month,
					DATE_ADD(af_last_dt, INTERVAL af_bill_cycle DAY) as next_day,
				af_ag_id, ag_cus_id, af_feat_id, af_feat_type, af_start_dt, af_end_dt, af_last_dt, UPPER(af_unit) as af_unit, af_unit_price, af_bill_cycle, UPPER(af_bill_cycle_unit) as af_bill_cycle_unit, ru_id, ru_old_value, ru_new_value
			FROM agreement_feature AS af
			INNER JOIN agreement a ON a.ag_id=af.af_ag_id
			LEFT JOIN rawusage r ON r.ru_af_id=af.af_id

			WHERE 1 AND (
			CASE WHEN af_bill_cycle_unit = 'd' THEN
				(
					DATE(DATE_ADD( af_last_dt, 
					INTERVAL af_bill_cycle DAY )) = $bill_dt 
				)
			WHEN (af_bill_cycle_unit = 'm' AND 
				( 
					( (DAYOFMONTH($bill_dt) = af_bill_day)
						OR 
					  (LAST_DAY($bill_dt) >= af_bill_day AND DAYOFMONTH($bill_dt) >= af_bill_day) 
					)
				)) THEN
				(
					DATE(DATE_ADD(af_last_dt, INTERVAL af_bill_cycle+1 MONTH) ) > $bill_dt 
					AND
					DATE(DATE_ADD(af_last_dt, INTERVAL af_bill_cycle MONTH) ) <= $bill_dt 
				)
			WHEN af_bill_cycle_unit = 'm' THEN
				(
					DATE( DATE_ADD( af_last_dt, 
									INTERVAL af_bill_cycle MONTH )) = $bill_dt 
				)
			ELSE
				(
					DATE( DATE_ADD( af_last_dt,
									INTERVAL af_bill_cycle DAY )) = $bill_dt 
				)
			END
			OR
			(DATE(af_end_dt) = $bill_dt) ) AND af_bill_schedule = 'O' AND af_closed= 0 ;" ;

		global $QFC ;
		$QFC->db->beginTrans() ;
		$recset = $QFC->db->fetchRowSet($sql, 'assoc') ;
		foreach( $recset as $rec )
		{
			$next_dt = null ;
			if( $rec['af_bill_cycle_unit'] == 'M' )
			{
				$next_dt = $rec['next_month'] ;
				$hours_to_next = $rec['hours_to_next_mb'] ;
				$days_to_next = $rec['days_to_next_mb'] ;
			}
			else if( $rec['af_bill_cycle_unit'] == 'D' )
			{
				$next_dt = $rec['next_day'] ;
				$hours_to_next = $rec['hours_to_next_db'] ;
				$days_to_next = $rec['days_to_next_db'] ;
			}
			if( ($rec['b_partial_m'] && $rec['af_bill_cycle_unit'] == 'M') || ($rec['b_partial_d'] && $rec['af_bill_cycle_unit'] == 'D') )
			{
				//if partial use agreement end dt..
				$next_dt = $rec['af_end_dt'] ;

				$hours = $rec['hours_to_end'] ;
				$days = $rec['days_to_end'] ;
			}
			else
			{
				$hours = $rec['hours_to_cur'] ;
				$days = $rec['days_to_cur'] ;
			}

//			$days_in_month = cal_days_in_month(CAL_GREGORIAN, Date('m', strtotime($rec['af_last_dt'])) , Date('Y', strtotime($rec['af_last_dt']))) ;
			$days_in_year = (Date('L', strtotime($rec['af_last_dt'])) ? 366 : 365) ;
			switch( $rec['af_unit'] )
			{
				case 'H' :
					$vdBillAmount = $rec['af_unit_price'] * $hours ;
					break ;
				case 'D' :
					$vdBillAmount = $rec['af_unit_price'] * $days ;
					break ;
				case 'M' :
					$vdBillAmount = _billAmountAcrossMonth($rec['af_last_dt'], $next_dt, $rec['af_unit_price']) ;
					break ;
				case 'Y' :
					$vdBillAmount = ($rec['af_unit_price'] / $days_in_year) * $days ;
					break ;
				case 'U' :
					$vdUsage = $rec['ru_new_value'] - $rec['ru_old_value'] ;
					$vdBillAmount = $vdUsage * $rec['af_unit_price'] ;
					$sqlu = "UPDATE rawusage SET ru_new_value = ru_old_value, ru_new_value=0 WHERE ru_id = '$rec[ru_id]'" ;
					$QFC->db->execute($sqlu) ;
					break ;
			}
			if( $rec['af_unit'] != 'U' )
			{
				$vdUsage = 0 ;
			}

			//only if there are some amount..
			if( $vdBillAmount )
			{
				//Run bill Query...
				$sqli = "INSERT INTO rawbilling( rb_ag_id, rb_cus_id, rb_feat_id, rb_feat_type, rb_dts, rb_dte, rb_unit, 
									rb_unit_price, rb_usage, rb_bill_amount, rb_deposit, rb_billed) VALUES
								('$rec[af_ag_id]', '$rec[ag_cus_id]', '$rec[af_feat_id]', '$rec[af_feat_type]', '$rec[af_last_dt]', '$next_dt', '$rec[af_unit]', 
									'$rec[af_unit_price]', '$vdUsage', '$vdBillAmount', '0', '0') ;" ;
				$QFC->db->execute($sqli) ;
			}

			//update last dt 
			$nextDtStr = Date('Y-m-d H:i:s', strtotime($next_dt)) ;
			$sqlul = "UPDATE agreement_feature SET af_last_dt='$nextDtStr' WHERE af_id=$rec[af_id] LIMIT 1" ;
			$QFC->db->execute($sqlul) ;
		}

		$QFC->db->commitTrans() ;
}
include 'dynatable.php' ;
?>