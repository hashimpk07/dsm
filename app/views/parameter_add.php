<link rel="stylesheet" media="screen" type="text/css" href="<?php echo baseUrl('assets/packages/colorpicker/css/colorpicker.css') ; ?>" />
<script type="text/javascript" src="<?php echo baseUrl('assets/packages/colorpicker/js/colorpicker.js');?>" ></script>
<div class="dvPageHeadingArea">
	<span class="heading"><?php //echo ( ($mode =='edit') ? 'Edit' : 'Add');?>Settings </span>

	<div style="margin: 1em 0px;margin-top: 5px;font-size: 17px;">
		<p style="background: #EEE; padding: 5px ;" >Set Color Scheme</p>
	</div>
</div>

<div class="addgrpwrap">

<form action="<?php echo @$url;?>" method="post" id="idFrmAdd" >
	<?php echo $this->formFields(); ?>

	<table width="100%" class="nostyle" style="margin: 20px 0;" >	
			<tr id="idColorSelectionTr">
				<td style="text-align: left; background-color:#FFFFFF !important;">Change Color</td> 
					<td style="background-color:#FFFFFF !important;">:</td>
					<td><input  type="text" readonly="readonly" style="border-radius: 20px; width: 20px; height: 20px; cursor:pointer; background-color: #<?php echo @$result['value'] ;?>; color:#<?php echo @$result['value'] ;?>" name="txtColor" id="txtColor" class="input-text" value="<?php echo @$result['value'] ;?>" />
				<br/>
			<label class="eMessage" id="eArea"></label>
				
				</td>
			</tr>
							
			<tr>
				<td style="background-color:#FFFFFF !important;"></td>
				<td style="background-color:#FFFFFF !important;"></td>
				<td><br/><input name="btnSubmit" type="submit" class="input-submit" value="Save" /></td>
			</tr>
		</table>
</form>
	
</div>

<script type="text/javascript">

	function facilityToggleColor(obj, target)
	{
		if(jQuery(obj).is(':checked') )
		{
			jQuery(target).show() ;
		}
		else
		{
			jQuery(target).hide() ;
		}
	}
	function doValidation()
	{
		return true;
	}

	/* submitForm( formName, beforeFunctionm, afterFunction, targetId, autofill json response); */
	submitForm( 'idFrmAdd', doValidation, function(data){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable'); setHightlightRow('<?php echo get_class($this);?>', data.__id ) ;}, 'idWorkArea', true);

	<?php
	$mrColor = '888888' ;
	if( @$result['value'] )
	{
		$mrColor = @$result['value'] ;
	}
	?>

	jQuery('#txtColor').css('background-color', '#<?php echo $mrColor ;?>' ) ;
	jQuery('#txtColor').ColorPicker({
		color: '#<?php echo $mrColor ;?>',
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			jQuery('#txtColor').css('background-color', '#' + hex);
			jQuery('#txtColor').css('color', '#' + hex);
			jQuery('#txtColor').val(hex);
		}
	});
</script>