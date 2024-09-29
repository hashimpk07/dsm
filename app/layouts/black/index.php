<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en" />
	<meta name="robots" content="noindex,nofollow" />
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo baseUrl(); ?>assets/css/reset.css" /> <!-- RESET -->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo baseUrl(); ?>assets/css/main.css" /> <!-- MAIN STYLE SHEET -->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo baseUrl(); ?>assets/css/2col.css" title="2col" /> <!-- DEFAULT: 2 COLUMNS -->
	<link rel="alternate stylesheet" media="screen,projection" type="text/css" href="<?php echo baseUrl(); ?>assets/css/1col.css" title="1col" /> <!-- ALTERNATE: 1 COLUMN -->
	<!--[if lte IE 6]><link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo baseUrl(); ?>assets/css/main-ie6.css" /><![endif]--> <!-- MSIE6 -->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo baseUrl(); ?>assets/css/style.css" /> <!-- GRAPHIC THEME -->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo baseUrl(); ?>assets/css/mystyle.css" /> <!-- WRITE YOUR CSS CODE HERE -->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo baseUrl(); ?>assets/css/user.css" /> <!-- WRITE YOUR CSS CODE HERE -->
	<style type="text/css">
	<?php
//		include fileUrl('../assets/css/user.php') ;
	?>
	</style>
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/themes/ui-lightness/jquery.ui.theme.css" /> <!-- WRITE YOUR CSS CODE HERE -->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/themes/ui-lightness/jquery.ui.datepicker.css" /> <!-- WRITE YOUR CSS CODE HERE -->

	<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/js/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/js/jquery-ui-1.10.2.custom.min.js"></script>

    <script type="text/javascript" src="<?php echo baseUrl(); ?>system/qf.js"></script>
	<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/switcher.js"></script>
	<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/toggle.js"></script>
	<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/ui.core.js"></script>
	<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/ui.tabs.js"></script>

	<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/ui/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/jquery-ui-timepicker-addon.js"></script>

	<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/jquery.form.js"></script>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	
	<script type="text/javascript">
	window.dhx_globalImgPath="<?php echo baseUrl('assets/packages/dhtmlx/codebase/imgs/');?>";
	</script>

	<link rel="stylesheet" type="text/css" href="<?php echo baseUrl('assets/packages/dhtmlx/codebase/dhtmlxcombo.css?rnd=2sd3');?>" />
	<script  src="<?php echo baseUrl('assets/packages/dhtmlx/codebase/dhtmlxcommon.js');?>"></script>
	<script  src="<?php echo baseUrl('assets/packages/dhtmlx/codebase/dhtmlxcombo.js');?>"></script>
	<script  src="<?php echo baseUrl('assets/packages/dhtmlx/codebase/ext/dhtmlxcombo_extra.js');?>"></script>


	<script type="text/javascript">
	$(document).ready(function(){
		$(".tabs > ul").tabs();
	});
	</script>
	<title><?php echo $QF->getPageTitle() ; ?> </title>
</head>

<body>
<?php 
$usr_grp_code= $QF->session->get('usr_grp_code'); 
$searchdt = date('d-m-Y') ; 
?>
<div id="main">
	<!-- Tray -->
	<div id="tray" class="box">

		

        <div>
			<p class="f-left box">
			<strong>
				<img style="vertical-align: middle;" height="60" src="<?php echo baseUrl('/assets/images/logo.png');?>" /> 
				<label>CRM</label>
			</strong>
		</p>

		<p class="f-right">
			<?php
			if( $QFC->session->get('usr_id') )
			{
			?>
			<span>user: </span>
			<a href="<?php echo siteUrl(); ?>settings"><?php echo $QFC->session->get('usr_alias');?> </a>
			<a href="<?php echo siteUrl(); ?>dashboard/logout" id="logout">Logout</a>
			<?php
			}
			?>
		</p>
            <p class="f-top" style="float: right; margin-top: 5px;" >
            </p>
        </div>

	</div> <!--  /tray -->

	<hr class="noscreen" />

	<!-- Menu -->
	<div id="menu" class="box">

		<ul class="box f-right" >
			<!--<li><a href="#"><span><strong>Visit Site &raquo;</strong></span></a></li>-->
		</ul>
		<?php
		function isActiveMenu($strCheck, $strRet='')
		{
			$url = currentUrl() ;
			if(preg_match('/^' . $strCheck . '/', $url))
			{
				return $strRet ;
			}
			return '' ;
		}
		?>
		<ul class="box" >
			<li class="<?php  echo isActiveMenu('dashboard', 'menu-active');?>"><a href="<?php echo siteUrl('dashboard');?>"><span><?php echo l('Home');?></span></a></li> <!-- Active -->
			<?php if($usr_grp_code){ ?>
			<li class="<?php echo isActiveMenu('customers', 'menu-active');?>"><a href="<?php echo siteUrl('customers');?>"><span><?php echo l('Customers');?></span></a></li>
			<?php } ?>
			<?php  if($usr_grp_code){ ?>
			<li class="<?php echo isActiveMenu('leads\/leads', 'menu-active');?>"><a href="<?php echo siteUrl('leads/leads');?>"><span><?php echo l('Leads');?></span></a></li>
			<?php }  ?>
			<?php if($usr_grp_code){ ?>
			<li class="<?php echo isActiveMenu('alerts', 'menu-active');?>"><a href="<?php echo siteUrl('alerts');?>"><span><?php echo l('Alerts');?></span></a></li>
			<?php } ?>
			<?php  if($usr_grp_code){ ?>
			<li class="<?php echo isActiveMenu('leads\/lead_report', 'menu-active');?>"><a href="<?php echo siteUrl('leads/lead_reports/customers');?>"><span><?php echo l('Lead Report');?></span></a></li>
			<?php }  ?>
			<?php  if($usr_grp_code){ ?>
			<li class="<?php echo isActiveMenu('templates\/templates', 'menu-active');?>"><a href="<?php echo siteUrl('templates/templates');?>"><span><?php echo l('Templates');?></span></a></li>
			<?php }  ?>
			<?php if($usr_grp_code){ ?>
			<li class="<?php echo isActiveMenu('unsubscribe', 'menu-active');?>"><a href="<?php echo siteUrl('unsubscribe/add');?>"><span><?php echo l('Unsubscribe');?></span></a></li>
			<?php } ?>
            <?php if($usr_grp_code=='S' || $usr_grp_code == 'E' ){ ?>
			<li class="<?php echo isActiveMenu('masters|employee', 'menu-active');?>" onmouseover="jQuery('#idsub').show();" onmouseout="jQuery('#idsub').hide();" style="position: relative;float: left;" class="<?php echo isActiveMenu('templates', 'menu-active');?>">
				<a href="javascript:void();"><span>Masters</span></a>
				<ul id="idsub" style="position: absolute; top:30px;left: 0; width: 100%; display: none;" class="submenu">
					<?php if($usr_grp_code=='S'){ ?>
					<li class="<?php echo isActiveMenu('employee', 'menu-active');?>"><a href="<?php echo siteUrl('employee');?>"><span><?php echo l('Executives');?></span></a></li>
					<?php }  ?>
					<li class="<?php echo isActiveMenu('masters\/index\/resident_status', 'menu-active');?>"><a href="<?php echo siteUrl('masters/index/resident_status');?>"><span><?php echo l('Resident Status');?></span></a></li>
					<li class="<?php echo isActiveMenu('masters\/index\/type', 'menu-active');?>"><a href="<?php echo siteUrl('masters/index/type');?>"><span><?php echo l('Property types');?></span></a></li>
					<li class="<?php echo isActiveMenu('masters\/index\/reference', 'menu-active');?>"><a href="<?php echo siteUrl('masters/index/reference');?>"><span><?php echo l('Lead references');?></span></a></li>
					<li class="<?php echo isActiveMenu('masters\/index\/mode', 'menu-active');?>"><a href="<?php echo siteUrl('masters/index/mode');?>"><span><?php echo l('Communication mode');?></span></a></li>
					<li class="<?php echo isActiveMenu('masters\/index\/reason', 'menu-active');?>"><a href="<?php echo siteUrl('masters/index/reason');?>"><span><?php echo l('Dropped reasons');?></span></a></li>
				</ul>
			</li>
			<?php } ?>
			<?php if($usr_grp_code=='S' || $usr_grp_code=='E' || $usr_grp_code=='R'){ ?>
			<li class="<?php echo isActiveMenu('settings', 'menu-active');?>"><a href="<?php echo siteUrl('settings');?>"><span><?php echo l('Settings');?></span></a></li>
			<?php } ?>            
		</ul>

	</div> <!-- /header -->

	<hr class="noscreen" />

	<!-- Columns -->
	<div id="cols" class="box">

		<!-- Aside (Left Column) -->
		<!--<div id="aside" class="box">

			<div class="padding box">

				<p id="logo"><a href="#"><img src="<?php echo baseUrl(); ?>assets/tmp/logo.gif" alt="Our logo" title="Visit Site" /></a></p>

				


			</div> 

			<ul class="box">
                            
                            
				<li id="submenu-active"><a href="#">Active Page</a></li>
				<li><a href="#">Lorem ipsum</a></li>
				<li><a href="#">Lorem ipsum</a></li>
			</ul>

		</div> --> <!-- /aside -->

		<hr class="noscreen" />

		<!-- Content (Right Column) -->
		<div style="display: none;position: fixed; top: 0px; left: 48%; background: white;" id="idWaitArea">
			<img src="<?php echo baseUrl(); ?>assets/images/loading.gif" />
		</div>
		<div style="clear: both"></div>
		
			<?php 
			/*
			if($usr_grp_code=='S' || $usr_grp_code=='E' || $usr_grp_code=='R')
			{ 
				ob_start() ;
				$QFC->loadLibrary('qnotifications')->show();
				$data = ob_get_clean() ;
				if( trim($data) )
				{
					?>
					<div id="idNotificationArea" style="width: 100%; "><?php echo $data;?></div>
					<?php
				}
			}
			 */
			?>
                <div class="clear"></div>
        <div id="idContentAreaBig" style="margin-left:14px">
			<?php echo $contents; ?>
		</div>
		 <!-- /content -->

	</div> <!-- /cols -->

	<hr class="noscreen" />

	<!-- Footer -->
	<div id="footer" class="box">

		<p class="f-right">Powered by <a target="_blank" href="http://www.qudratom.com/">Qudratom R&D</a></p>

	</div> <!-- /footer -->

</div> <!-- /main -->

</body>
</html>


