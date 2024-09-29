<div class="dvPageHeadingArea">
	<span class="heading"><?php echo ( ($mode =='edit') ? 'Edit' : 'Add');?> Language</span>

	<div class="subheading">
		<div class="bulkactions">
			<span title="List" class="flaticon flaticon-list" onclick="onListIconClick('idListArea<?php echo get_class($this);?>','<?php echo siteUrl('department/page'); ?>', {}, 'idContentAreaDetect')" ></span>
		</div>
	</div>
</div>

<div class="viewgrpwrap">

<form action="<?php echo @$url;?>" method="post" id="idFrmAdd<?php echo get_class($this);?>" >
	<?php echo $this->formFields(); ?>

		<table width="100%" class="nostyle" >
			<tr>
				<td>Name<span class="red">*</span></td>
					<td>:</td>
					<td><input type="text" name="txtName" id="txtName" class="initfocusfield input-text" value="<?php echo @$result['name'];?>"/>
				<br/>
				<label class="eMessage" id="eName"></label>
				</td>
			</tr>
			<tr>
				<td>Font Name<span class="red">*</span></td>
					<td>:</td>
					<td><input type="text" name="txtFont" id="txtFont" class="initfocusfield input-text" value="<?php echo @$result['font'];?>"/>
				<br/>
				<label class="eMessage" id="eFont"></label>
				</td>
			</tr>
			<tr>
				<td>Direction<span class="red">*</span></td>
					<td>:</td>
					<td>
						<select name="selDirection">
							<?php
							$ar = array(
								'r' => "Right To Left",
								'l' => "Left To Right",
							);
							foreach( $ar as $k => $v )
							{
								$sel = '' ;
								if( @$result['dir'] == $k )
								{
									$sel = "selected='selected'" ;
								}
							?>
							<option <?php echo $sel;?> value="<?php echo $k;?>"><?php echo $v;?></option>
							<?php
							}
							?>
						</select>
				<br/>
				<label class="eMessage" id="eFont"></label>
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
			'#txtName' :{ func : 'required()' , errfield : '#eName', errmsg  : 'Name not specified' },
			'#txtFont':{ func : 'notvalue("0")' , errfield : '#eFont', errmsg  : 'Font not specified' }
		};
		if( validateForm(a, '#idErrorSummary' ) )
		{
			return true ;
		}
		return false;
	}

	/* submitForm( formName, beforeFunctionm, afterFunction, targetId, autofill json response); */
	submitForm( 'idFrmAdd<?php echo get_class($this);?>', doValidation, function(data){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable'); setHightlightRow('<?php echo get_class($this);?>', data.__id ) ;}, 'idWorkArea', true);

</script>