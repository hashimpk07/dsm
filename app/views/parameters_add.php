<div class="dvPageHeadingArea">
	<span class="heading"><?php echo ( ($mode =='edit') ? 'Edit' : 'Add');?> Parameter</span>

	<div class="subheading">
		<div class="bulkactions">
			<span title="List" class="flaticon flaticon-list" onclick="onListIconClick('idListArea<?php echo get_class($this);?>','<?php echo siteUrl('parameters/page'); ?>', {}, 'idContentAreaDetect')" ></span>
		</div>
	</div>
</div>

<div class="addgrpwrap">

<form action="<?php echo @$url;?>" method="post" id="idFrmAdd" >
	<?php echo $this->formFields(); ?>

		<table width="100%" class="nostyle" >
			
            <tr>
				<td>Title <span class="red">*</span></td>
					<td>:</td>
					<td><input type="text" name="txtTitle" id="txtTitle" class="initfocusfield input-text" value="<?php echo @$result['pm_title'];?>"/>
				<br/>
				<label class="eMessage" id="eTitle"></label>
				</td>
			</tr>
            
            <tr>
				<td>Code <span class="red">*</span></td>
					<td>:</td>
					<td><input type="text" name="txtCode" id="txtCode" class="input-text" value="<?php echo @$result['pm_code'];?>"/>
				<br/>
				<label class="eMessage" id="eCode"></label>
				</td>
			</tr>
			
			<tr>
				<td>Value</td>
					<td>:</td>
					<td><input type="text" name="txtValue" id="txtValue" class="input-text" value="<?php echo @$result['pm_value'];?>" />
				<br/>
				<label class="eMessage" id="eValue"></label>
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
			'#txtTitle' :{ func : 'required()' , errfield : '#eTitle', errmsg  : 'Invalid parameter title' }	,
            '#txtCode' :{ func : 'required()' , errfield : '#eCode', errmsg  : 'Invalid parameter code' }
			
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