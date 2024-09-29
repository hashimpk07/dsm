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
		
		<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
		<link type="text/css" rel="stylesheet" href="<?php echo baseUrl(); ?>assets/css/jquery-ui.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/themes/ui-lightness/jquery.ui.theme.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/themes/ui-lightness/jquery.ui.datepicker.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/themes/ui-lightness/jquery-ui-silver.css" />

		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/js/jquery-ui-1.10.2.custom.min.js"></script>
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/ui/jquery.ui.widget.js"></script>
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/jquery/development-bundle/ui/jquery.ui.datepicker.js"></script>
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/js/jquery-ui-timepicker-addon.js"></script>

		<style>
.viewgrpwrap tr td {
    opacity: 0.8;
    padding: 3px 5px;
    text-align: left;
    vertical-align: middle;
}
.viewgrpwrap tr td:nth-child(2) {
    padding: 3px 5px;
    vertical-align: middle;
    width: 10px;
}
.viewgrpwrap tr td:first-child {
    background: none repeat scroll 0 0 #f8f8f8;
    font-size: 1em;
    letter-spacing: 1px;
    min-width: 27%;
    opacity: 1;
    padding: 3px 5px;
    text-align: right;
    vertical-align: middle;
    white-space: nowrap;
    width: 1px;
}
.viewgrpwrap {
    width: 100%;
}
			.heading
			{
				display: none;
			}
			#idContentAreaBig table
			{
				width: 100%;
			}
			table table
			{
				border: 1px solid silver;
				margin: 10px 1cm;
			}
			table table th
			{
				background: #EEE ;
				text-align: center;
			}
			table table td
			{
				text-align: center;
			}
			@font-face {
				font-family: Roboto;
				src: url(<?php echo baseUrl('assets/fonts/roboto.ttf');?>);
			}
		</style>

	</head>
    <body style="font-family: tahoma, sans serif">
		<div style="float: left; width: 22cm">

		<div class="header navbar navbar-fixed-top" style="font-family: Roboto; " >
			<div class="header">
				<span id="idSuccessMsg" onclick="jQuery(this).hide()"></span>
				<span id="idFailureMsg" onclick="jQuery(this).hide()"></span>
				<span id="idModalDialogBox" ></span>

				<div class="logo" style="float: left; margin-left: 100px">
					<img src="<?php echo baseUrl('assets/images/printlogo.png');?>" />
				</div>
				<div class="title" style="float: right; padding-top: 15px;">
					<p style="text-align: right;">
						Centre A (A division of Alapatt Properties Pvt.Ltd) <br/>
						7th Floor, Alapatt Heritage Building, MG Road, Kochi - 682035
					</p>
				</div>

                <div style="clear: both;"></div>
				
				<!--<hr style="width:100%; float: left;" color="#EEE"/>-->

                <div id="idContentAreaBig" style="width: 22cm; font-family: sans-serif" class="pure-u-1" >        
				<?php echo $contents;?> 
				</div>
				
			</div>
			
			<?php /*
			<!--<hr style="width:100%; float: left;" color="#EEE" />-->
			<div style="margin: 1cm 1cm; width: 22cm; font-size: 0.9em">
				PAN No – AAMCA5620R <br/>
				Service Tax code – AAMCA5620RSD001 <br/> <br/>
				<b>Payment Method:</b> <br/>
				Cheque <br/>
					<div style="padding-left:0.5cm;">Payable to ‘Alapatt Properties Pvt. Ltd’  <br/></div>
					<br/>
				Direct Deposit to Bank:  <br/>
					<div style="padding-left:0.5cm;">Federal Bank <br/>
					Branch: Ernakulam/Broadway <br/>
					IFSC: FDRL0001283 <br/>
					A/c Number:12830200018527 <br/></div> 
				<br/>
				<div style="font-size: 0.9em; text-align: left">
					<br/><br/>
					<span style="float: left; padding: 3px; border: 1px solid #333; font-weight: bold;">
					Note: Please make your payments in seven days to avoid late payment charges
					</span>
					<br/><br/>
					This is a computer generated invoice, stamp and signature not required.
				</div>
			</div>
			 */ ?>
		</div>
				<script>
//				window.print() ;
//				window.close() ;
				</script>
	</body>
</html>
