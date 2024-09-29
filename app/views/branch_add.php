
<div class="dvPageHeadingArea">
	<span class="heading"><?php echo ( ($mode =='edit') ? 'Edit' : 'Add');?> Branch</span>

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
				<td>Branch Name<span class="red">*</span></td>
					<td>:</td>
					<td><input type="text" name="txtName" id="txtName" class="initfocusfield input-text" value="<?php echo @$result['name'];?>"/>
				<br/>
				<label class="eMessage" id="eName"></label>
				</td>
			</tr>
			<tr>
				<td>Branch Code<span class="red">*</span></td>
					<td>:</td>
					<td><input type="text" name="txtCode" id="txtCode" class=" input-text" value="<?php echo @$result['code'];?>"/>
				<br/>
				<label class="eMessage" id="eCode"></label>
				</td>
			</tr>
			
		<tr>
			<td>Country</td>
				<td>:</td>
				<td><select onchange="getData('<?php echo siteUrl('ajax/state_options');?>/' + this.value + '/<?php echo @$result['state_id'];?>', {}, 'selState' )" name="cbAddrCountry" id="cbAddrCountry"> 
					<option value='0'>--Select--</option>
				<?php
					foreach( $countries as $k => $v )
					{
						$sel = '' ;
						if( $v['id'] == @$result['country_id'])
						{
							$sel = 'selected="selected" ' ;
						}
				?>
					<option <?php echo $sel;?> value="<?php echo $v['id'] ;?>" ><?php echo $v['name'];?></option>
				<?php
					}
				?>					
				</select>
				
			<br/>
			<label class="eMessage" id="eSelCountry"></label>
			</td>
		</tr>
		<tr>
			<td>State</td>
				<td>:</td>
				<td><select onchange="getData('<?php echo siteUrl('ajax/city_options');?>/' + this.value + '/<?php echo @$result['city_id'];?>', {}, 'selCity' )" name="selState" id="selState"> 
					<option value="0" >--Select--</option>
					<?php
						if($mode=='edit' || $mode == 'convert')
						{
							
						foreach( @$states as $k => $v )
						{
							$selc = '' ;
							if( $v['id'] == @$result['state_id'])
							{
								$selc = 'selected="selected" ' ;
							}
					?>
						<option <?php echo $selc;?> value="<?php echo $v['id'] ;?>" ><?php echo $v['name'];?></option>
					<?php
						} }
					?>	
				</select>
			<br/>
			<label class="eMessage" id="eSelState"></label>
			</td>
		</tr>
		<tr>
			<td>City</td>
				<td>:</td>
				<td><select name="selCity" id="selCity">
					<option value="0" >--Select--</option>
					<?php
						if($mode=='edit' || $mode == 'convert')
						{
							
						foreach( @$cities as $k => $v )
						{
							$selc = '' ;
							if( $v['id'] == @$result['city_id'])
							{
								$selc = 'selected="selected" ' ;
							}
					?>
						<option <?php echo $selc;?> value="<?php echo $v['id'] ;?>" ><?php echo $v['name'];?></option>
					<?php
						} }
					?>	
				</select>
			<br/>
			<label class="eMessage" id="eSelCity"></label>
			</td>
		</tr>
        
			
				<tr>
				<td>Active Screen  </td>
					<td>:</td>
					<td><select  name="cbScreen" id="cbScreen">
						<option value='0'>--Select--</option>
					<?php
						foreach( $screens as $k => $v )
						{
							$sel = '' ;

							if( $v['id'] == @$result['screen_id'])
							{
								$sel = 'selected="selected" ' ;
							}
							?>
							<option <?php echo $sel; ?> value="<?php echo $v['id'] ;?>" ><?php echo $v['name'];?></option>
							<?php
						}
						?>
					</select>
				<br/>
				<label class="eMessage" id="eScreen"></label>
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
			'#txtCode' :{ func : 'required()' , errfield : '#eCode', errmsg  : 'Code not specified' },
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