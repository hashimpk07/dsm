<div class="dvPageHeadingArea">
	<span class="heading"><?php echo ( ($mode =='edit') ? 'Edit' : 'Add');?> Location</span>

	<div class="subheading">
		<div class="bulkactions">
			<span title="List" class="flaticon flaticon-list" onclick="onListIconClick('idListArea<?php echo get_class($this);?>','<?php echo siteUrl('location/page'); ?>', {}, 'idContentAreaDetect')" ></span>
		</div>
	</div>
</div>

<div class="addgrpwrap">

<form action="<?php echo @$url;?>" method="post" id="idFrmAdd" >
	<?php echo $this->formFields(); ?>

		<table width="100%" class="nostyle" >
			
            <tr>
				<td>Name <span class="red">*</span></td>
					<td>:</td>
					<td><input type="text" name="txtName" id="txtName" class="initfocusfield input-text" value="<?php echo @$result['name'];?>"/>
				<br/>
				<label class="eMessage" id="eName"></label>
				</td>
			</tr>
          
			<tr>
				<td></td>
				<td></td>
				<td><input name="btnSubmit" type="submit" class="input-submit" value="Save" /></td>
			</tr>
		</table>
</form>
	
</div>

<script type="text/javascript">
	
	function doValidation()
	{
		var a = { 
			'#txtName' :{ func : 'required()' , errfield : '#eName', errmsg  : 'Invalid location name' }
				
		};
		if( validateForm(a, '#idErrorSummary' ) )
		{
			return true ;
		}
		return false;
	}

	/* submitForm( formName, beforeFunctionm, afterFunction, targetId, autofill json response); */
	submitForm( 'idFrmAdd', doValidation, function(data){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable'); setHightlightRow('<?php echo get_class($this);?>', data.__id ) ;}, 'idWorkArea', true);

</script>