<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="A layout example that shows off a responsive email layout.">
		<title><?php
		global $QFC ;
		echo $QFC->getPageTitle(); ?> </title>
		<script type="text/javascript">
			var QFS_SITE_URL = '<?php echo siteUrl();?>' ;
		</script>

		<link rel="stylesheet" href="<?php echo baseUrl(); ?>assets/tti/css/layouts/pure.css">
		<!--[if lte IE 8]>
			<link rel="stylesheet" href="<?php echo baseUrl(); ?>/assets/tti/css/layouts/email-old-ie.css">
		<![endif]-->
		<!--[if gt IE 8]><!-->
        <link rel="stylesheet" href="<?php echo baseUrl(); ?>assets/tti/css/layouts/email.css">
		<!--<![endif]-->

		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/js/jquery-1.9.1.js"></script>
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/jquery.form.js"></script>
		<script type="text/javascript" src="<?php echo baseUrl(); ?>system/qf.js"></script>

		<link type="text/css" rel="stylesheet" href="<?php echo baseUrl(); ?>assets/css/jquery-ui.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/themes/ui-lightness/jquery.ui.theme.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/themes/ui-lightness/jquery.ui.datepicker.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/themes/ui-lightness/jquery-ui-silver.css" />

		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/js/jquery-ui-1.10.2.custom.min.js"></script>
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/chosen.jquery.min.js"></script>
		
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/ui/jquery.ui.widget.js"></script>
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/ui/jquery.ui.datepicker.js"></script>
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/jquery-ui-timepicker-addon.js"></script>
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/jquery.comiseo.daterangepicker.min.js"></script>
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/moment.min.js"></script>
		
<!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->
<link rel="stylesheet" type="text/css" href="<?php echo baseUrl(); ?>assets/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php echo baseUrl(); ?>assets/css/colorpicker.css">
<script src="<?php echo baseUrl(); ?>assets/js/jquery.popupoverlay.js" ></script>

<script src="<?php echo baseUrl(); ?>assets/js/colorpicker.js" ></script>


		<style type="text/css">
		<?php include fileUrl('assets/css/user.php') ; ?>
		</style>
	</head>
	<body>
		<div class="header navbar navbar-fixed-top">
			<div class="header">
				<span id="idContentAreaMenuHightlight" style="display: none;" ></span> <?php /*A place holder to retrieve custom bg image from user.php css */?>
				<span id="idSuccessMsg" onclick="jQuery(this).hide()"></span>
				<span id="idFailureMsg" onclick="jQuery(this).hide()"></span>
				<span id="idModalDialogBox" ></span>

				<div class="logo">
					<h1 style="cursor: pointer; float: left;position: absolute; top: 0; color: #0073B2; left: 10px;" onclick="getData('<?php echo siteUrl('dashboard/board');?>', {}, 'idContentAreaSmall' );" >
						DMS
					</h1>
				</div>
				<div class="header-actions">
					<h1 style="float: right;position: absolute; top: 0; color: #0073B2; right: 10px;">
						<div class="pop-notification-wrap" >
							<span title="Notifications" class="pop-notification-count pop-notification-icon"><?php echo @$notification_count;?></span>
							<div id="idNotificationArea">
								<?php // $QFC->loadView('notifications.php'); ?>
							</div>
						</div>

						<b class="pop-profile-icon" style="font-size: 15px; cursor: pointer;" onclick="return false;actionEdit( '<?php echo siteUrl('employee/profile/'. $QFC->session->get('usr_id') ) ; ?>', {}, 'idContentAreaSmall');" ><?php echo $QFC->session->get('usr_alias');?></b>
						<div class="pop-profile-wrap" >
							<div id="idProfileArea">
								<?php
								$qempid = $QFC->session->get('usr_id') ;
								
								$QFC->vars['mode'] = 'edit' ;
								$QFC->setArg('editId', $qempid) ;
								$QFC->vars['url'] = siteUrl('user/profile' . '/' . $qempid ) ;
								$QFC->vars['result'] = $QFC->getModel('user_model')->getDetails($qempid) ;
								//$QFC->vars['countries'] = $QFC->getModel('countries_model')->get() ;
								$QFC->loadView('user_profile.php');  ?>
							</div>
						</div>

						<a onclick="actionView('<?php echo siteUrl('login/logout'); ?>', {}, 'idContentAreaSmall')" href="javascript:void(0)" >
							<span style="height: 25px; line-height: 25px; font-size: 15px; color: red; vertical-align: middle; font-weight: bold;" class="flaticon flaticon-logout"></span>
						</a>
					</h1>
				</div>
			</div>
		</div>
		
		<div id="layout" class="content pure-g">
			<div id="idContentAreaMenu" class="pure-u">
				<div class="nav-inner">
					<div class="pure-menu pure-menu-open">
						<ul class="menu-first">

                            <?php if($QFC->session->get('usr_grp_code') ) 
								{ ?>
                            <li title="Inventory" >
                               <!-- <a onclick="onSubmenuClick('idMenuInventory', this)" href="javascript:void(0);"><span class="flaticon flaticon-inventory"></span><span>Inventory</span>
									<span class="menuarrow flaticon-downarrow" />
								</a>-->
                                <ul class="pure-submenu mobile-submenu" id="idMenuInventory" style="display: block;">

									<?php /* if($QFC->accessAllowed('canvas', 'page',array('normal') ) ) { ?>
                                    <li title="Stock Transfer" onclick="actionView('<?php echo siteUrl('canvas'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-inventorytransfer"></span>
											<span>Canvas</span></a></li>

									<?php } */ ?>

								
								
									<?php if($QFC->accessAllowed('telecast', 'page' ) ) { ?>
                                    <li title="Department" onclick="actionView('<?php echo siteUrl('telecast/page'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-reference"></span>
											<span>Telecast</span></a></li>
										<?php }?>

									<?php /*
									  <?php if($QFC->session->get('usr_grp_code')== QC_USR_SUPERADMIN) { ?>
									  <?php if($QFC->accessAllowed('user_group', 'page' ) ) { ?>
									  <li title="Department" onclick="actionView('<?php echo siteUrl('user_group/page'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-items"></span>
									  <span>Groups</span></a></li>
									  <?php }}?>
									 * 
									 */ ?>

											
										<?php if($QFC->accessAllowed('content', 'page' ) ) { ?>
                                    <li title="Categories" onclick="actionView('<?php echo siteUrl('content/page'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-excel"></span>
											<span>Contents</span></a></li>
									<?php } ?>
											
									<?php if($QFC->accessAllowed('screen', 'page' ) ) { ?>
                                    <li title="Items" onclick="actionView('<?php echo siteUrl('screen/page'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-communication"></span>
											<span>Screens</span></a></li>
									<?php } ?>
															
											
									<?php if($QFC->accessAllowed('branch', 'page' ) ) { ?>
                                    <li title="Department" onclick="actionView('<?php echo siteUrl('branch/page'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-category"></span>
											<span>Branches</span></a></li>
										<?php }?>
															
											
									<?php if($QFC->accessAllowed('branch_group', 'page' ) ) { ?>
                                    <li title="Groups" onclick="actionView('<?php echo siteUrl('branch_group/page'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-masters"></span>
											<span>Branch Groups</span></a></li>
										<?php }?>

								 <?php
								 if($QFC->accessAllowed('user', 'page') ) { ?>
									<li title="Employees" onclick="actionView('<?php echo siteUrl('user/page'); ?>', {}, 'idContentAreaSmall')" >
										<a href="javascript:void(0);"><span class="flaticon flaticon-employee"></span>
											<span>Operators</span></a>
									</li>
								<?php } ?>	
									
									<?php
									if( $QFC->session->get('usr_grp_code') == QC_USR_SUPERADMIN) {
									if($QFC->accessAllowed('privilege_group', 'page' ) ) { ?>
                                    <li title="Department" onclick="actionView('<?php echo siteUrl('privilege_group/page'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-customeragreement"></span>
											<span>Privilege Groups</span></a></li>
										<?php }
									}
									?>
											
									<?php if($QFC->accessAllowed('language', 'page' ) ) { ?>
                                    <li title="Department" onclick="actionView('<?php echo siteUrl('language/page'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-language"></span>
											<span>Languages</span></a></li>
										<?php }?>
											
									<?php if($QFC->accessAllowed('screen_log', 'page' ) ) { ?>
                                    <li title="Screen Log" onclick="actionView('<?php echo siteUrl('screen_log/page'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-report"></span>
											<span>Screen Log</span></a></li>
										<?php }?>

								<?php if($QFC->accessAllowed('parameter', 'page' ) ) { ?>
                                    <li title="Settings" onclick="actionView('<?php echo siteUrl('parameter/page'); ?>', {}, 'idContentAreaSmall')" ><a href="javascript:void(0);"><span class="flaticon flaticon-parameters"></span>
											<span>Settings</span></a></li>
									<?php } ?>

                                </ul>
                            </li>
							<?php
							}
							?>
						</ul>
					</div>
				</div>
				<div class="nav-bottom" ><div class="pure-menu pure-menu-open" >

					</div>

				</div>
			</div>

			<!-- Content (Right Column) -->
			<div style="z-index: 9999;display: none;position: fixed; top: 0px; left: 48%; background: white;" id="idWaitArea">
				<img src="<?php echo baseUrl(); ?>assets/images/loading.gif" />
			</div>

			<div id="idContentAreaSmall" class="pure-u-1" style="width: 100%">
				<?php echo $contents;?>
			</div>

			<div id="idContentAreaBig" class="pure-u-1" style="display: none;">
			</div>

			<input type="hidden" value="" id="idContentAreaClicked" />

			<script type="text/javascript">
				jQuery('.pop-notification-icon').click(function(){
					jQuery('.pop-notification').slideToggle('fast') ;
					jQuery('.pop-profile').slideUp('fast') ;
					jQuery('#idDashFloorPopup').fadeOut('fast');
					jQuery('#idDashSchedulePopup').fadeOut('fast');
					return false ;
				}) ;
				jQuery('.pop-profile-icon').click(function(){
					//jQuery('.pop-profile').slideToggle('fast') ;
					jQuery('.pop-profile').show() ;
					jQuery('.pop-notification').slideUp('fast') ;
					jQuery('#idDashFloorPopup').fadeOut('fast');
					jQuery('#idDashSchedulePopup').fadeOut('fast');
					return false ;
				}) ;

				jQuery('#idContentAreaSmall').mousedown(function() {
					jQuery('#idContentAreaClicked').val('idContentAreaSmall');
				});
				jQuery('#idContentAreaBig').mousedown(function() {
					jQuery('#idContentAreaClicked').val('idContentAreaBig');
				});
				jQuery('#idContentAreaMenu').mousedown(function() {
					jQuery('#idContentAreaClicked').val('idContentAreaMenu');
				});
				jQuery('#idDashSchedulePopup').mousedown(function() {
					jQuery('#idContentAreaClicked').val('idContentAreaMenu');
				});
				//Excape jobs
				jQuery('body').click(function(){
					jQuery('.pop-notification').slideUp();
					jQuery('.pop-profile').slideUp();
					jQuery('#idDashFloorPopup').fadeOut('fast');
					jQuery('#idDashSchedulePopup').fadeOut('fast');
				});
				jQuery('.pop-profile, .pop-notification, #idDashFloorPopup, #idDashSchedulePopup').click(function(e){
              	e.stopPropagation();
				});
				jQuery('.pop-profile .closebtn').click(function(e){
					jQuery('.pop-profile').slideUp();
				});
				jQuery('.pop-notification .closebtn').click(function(e){
					jQuery('.pop-notification').slideUp();
				});
				jQuery('#idDashFloorPopup .closebtn').click(function(e){
					jQuery('#idDashFloorPopup').fadeOut('fast');
				});
				jQuery('#idDashSchedulePopup .closebtn').click(function(e){
					jQuery('#idDashSchedulePopup').fadeOut('fast');
				});
			</script>
		</div>

		<?php /*
		<script src="<?php echo baseUrl(); ?>assets/tti/js/yui.js"></script>
		<script>
				YUI().use('node-base', 'node-event-delegate', function(Y) {

					var menuButton = Y.one('.nav-menu-button'),
							nav = Y.one('#idContentAreaMenu');

					// Setting the active class name expands the menu vertically on small screens.
					menuButton.on('click', function(e) {
						nav.toggleClass('active');
					});

					// Your application code goes here...
				});
		</script>

		<script>
			YUI().use('node-base', 'node-event-delegate', function(Y) {
				// This just makes sure that the href="<?php echo baseUrl(); ?>/assets/tti/#" attached to the <a> elements
				// don't scroll you back up the page.
				Y.one('body').delegate('click', function(e) {
					e.preventDefault();
				}, 'a[href="<?php echo baseUrl(); ?>assets/tti/#"]');
			});

			//Bind first level menu hightlights
			jQuery('.menu-first li a').on('click', function() {
				jQuery('.menu-first li a').css('background-color', 'transparent');
				jQuery(this).css('background-color', jQuery('#idContentAreaMenuHightlight').css('background-color') ) ;
			});

		</script>
		 * 
		 */?>


		<!--  Popup Box -->
		<div id="idPopupSubmit" style=" display: none; padding: 10px;">
		</div>
		<div id="idPopupSubmitContainer" style="display: none;" onclick="popupSubmitHide()">

		</div>

	</body>
</html>