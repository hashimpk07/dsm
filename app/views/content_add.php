<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<div class="dvPageHeadingArea">
	<span class="heading"><?php
	echo ( ($mode =='edit') ? 'Edit' : 'Add');
	switch($classification) {
		case 'G' :
				echo ' Global Message' ;
			break ;
		case 'B' :
				echo ' Branch Message' ;
			break ;
		default :
				echo ' Content' ;
			break ;
	}
	?>
	</span>

	<div class="subheading">
		<div class="bulkactions">
			<span title="List" class="flaticon flaticon-list" onclick="onListIconClick('idListArea<?php echo get_class($this);?>','<?php echo siteUrl('content/page'); ?>', {}, 'idContentAreaDetect')" ></span>
		</div>
	</div>
</div>

<div class="addgrpwrap">

<form action="<?php echo @$url;?>" method="post" id="idFrmAdd" >
	<?php echo $this->formFields(); ?>

	<?php
	$result['data'] = @$result['lang_data'][QC_LANGUAGE_DEFAULT]['data'] ;
	?>
		<input type="hidden" name="hidClassification" value="<?php echo @$classification;?>" />

		<table width="100%" class="nostyle" >
			<tr>
				<td>Content Name<span class="red">*</span> </td>
				<td></td>
				<td>
					<input type="text" name="txtTitle" id="txtTitle" value="<?php echo @$result['title'] ?>" />
					<br/>
				<label class="eMessage" id="eTitle"></label>
				</td>
			</tr>

            <tr>
				<td>Type <span class="red">*</span></td>
					<td>:</td>
					<td>
						<select name="selType" id="selType" onchange="onTypeChange(this);" >
							<option value="0">Select</option>
						<?php

						foreach( $types as $k => $v )
						{
							$sel = '' ;
							if( $k == @$result['type'] )
							{
								$sel = "selected='selected'";
							}
							?>
							<option <?php echo $sel; ?> value="<?php echo $k;?>"><?php echo $v;?></option>
							<?php
						}
						?>
						</select>
				<br/>
				<label class="eMessage" id="eType"></label>
				</td>
			</tr>
			<tr class="clsTypeI type-rows" style="<?php echo (( @$result['type'] == QC_CONTENT_TYPE_I ) ? 'display:table-row' : '') ;?>" >
				<td>Image</td>
				<td></td>
				<td>
					<input type="file" name="filDataImage" />
				</td>
			</tr>
			<tr class="clsTypeIU type-rows" style="<?php echo (( @$result['type'] == QC_CONTENT_TYPE_IU ) ? 'display:table-row' : '') ;?>" >
				<td>Image URL</td>
				<td></td>
				<td>
					<input type="text" name="txtDataImageUrl" value="<?php echo @$result['data'];?>" />
				</td>
			</tr>




			<tr class="clsTypeV type-rows" style="<?php echo (( @$result['type'] == QC_CONTENT_TYPE_V ) ? 'display:table-row' : '') ;?>" >
				<td>Video</td>
				<td></td>
				<td>
					<input type="file" name="filDataVideo" />
				</td>
			</tr>
			<tr class="clsTypeVU type-rows" style="<?php echo (( @$result['type'] == QC_CONTENT_TYPE_VU ) ? 'display:table-row' : '') ;?>" >
				<td>Video URL</td>
				<td></td>
				<td>
					<input type="text" name="txtDataVideoUrl" value="<?php echo @$result['data'];?>" />
				</td>
			</tr>


			<?php
			foreach( $languages as $lang )
			{
			?>
			<tr class="clsTypeT type-rows" style="<?php echo (( @$result['type'] == QC_CONTENT_TYPE_T ) ? 'display:table-row' : '') ;?>" >
				<td>Text <?php echo ' - ' . $lang['name'];?></td>
				<td></td>
				<td>
					<?php
					$data = (( @$result['type'] == QC_CONTENT_TYPE_T ) ? @$result['lang_data'][$lang['id']]['data'] : '') ;
					?>
					<textarea name="txtDataText[<?php echo @$lang['id']; ?>]" ><?php echo $data;?></textarea>
				</td>
			</tr>
			<?php
			}
			?>

			<tr class="clsTypeH type-rows" style="<?php echo (( @$result['type'] == QC_CONTENT_TYPE_H ) ? 'display:table-row' : '') ;?>" >
				<td>HTML</td>
				<td></td>
				<td>
					<textarea name="txtDataHtml" ><?php echo @$result['data'];?></textarea>
				</td>
			</tr>

			<?php
			foreach( $languages as $lang )
			{
			?>
			<tr class="clsTypeS type-rows" style="<?php echo (( @$result['type'] == QC_CONTENT_TYPE_S ) ? 'display:table-row' : '') ;?>" >
				<td>Scrolling Text <?php echo ' - ' . $lang['name'];?></td>
				<td></td>
				<td>
					<?php
					$data = (( @$result['type'] == QC_CONTENT_TYPE_S ) ? @$result['lang_data'][$lang['id']]['data']: '') ;
					?>
					<textarea name="txtDataScrollingText[<?php echo @$lang['id']; ?>]" ><?php echo $data ;?></textarea>
				</td>
			</tr>
			<?php
			}
			?>


			<tr>
				<td>Save for future</td>
				<td></td>
				<td>
					<input type="checkbox" name="cbFuture" value="1" <?php echo (@$result['future'] ? 'checked="checked"' : ''); ?> />
				</td>
			</tr>

			<?php if( $classification != 'G' ) { ?>
			<tr>
				<td>Branch <span class="red">*</span></td>
					<td>:</td>
					<td>
						<select name="selBranch" id="selBranch" onchange="getData('<?php echo siteUrl('ajax/branch_screen_options');?>/' + this.value , {}, 'idSelScreen' )" >
							<option value="0">Select</option>
						<?php
						foreach( $branches as $b )
						{
							$sel = '' ;
							if( $b['id'] == @$result['branch_id'])
							{
								$sel = 'selected="selected"' ;
							}
							?>
							<option <?php echo $sel;?> value="<?php echo $b['id'];?>"><?php echo $b['name'];?></option>
							<?php
						}
						?>
						</select>
				<br/>
				<label class="eMessage" id="eBranch"></label>
				</td>
			</tr>
			<?php
			}
			?>

			<?php
			//Edit..
			?>

			<?php
			if( $mode !='edit' )
			{
				if( $classification != 'G'  ) { ?>
			<tr>
				<td colspan="3" class="grpheading">Schedule Content</td>
				<td></td>
			</tr>


			<tr>
				<td>Screen</td>
					<td>:</td>
					<td>
						<select name="selScreen" id="idSelScreen" onchange="getData('<?php echo siteUrl('ajax/screen_window_options');?>/' + this.value , {}, 'idSelWindow' )"  >
							<option value="">Select</option>
						<?php
						if(is_array(@$screens) )
						{
							foreach( @$screens as $b )
							{
								$sel = '' ;
								if( $b['id'] == @$result['screen_id'])
								{
									$sel = ' selected="selected" ' ;
								}
								?>
								<option <?php echo $sel;?> value="<?php echo $b['id'];?>"><?php echo $b['name'];?></option>
								<?php
							}
						}
						?>
						</select>
				<br/>
				<label class="eMessage" id="eName"></label>
				</td>
			</tr>
			<tr>
				<td>Window </td>
					<td>:</td>
					<td>
						<select name="selWindow" id="idSelWindow" >
							<option style="color:red" value="">Select</option>
						</select>
				<br/>
				<label class="eMessage" id="eName"></label>
				</td>
			</tr>

			<tr>
				<td>From</td>
					<td>:</td>
					<td>
						<input type="text" name="txtFrom" id="txtFrom" />
				<br/>
				<label class="eMessage" id="eName"></label>
				</td>
			</tr>
			<tr>
				<td>To</td>
					<td>:</td>
					<td>
						<input type="text" name="txtTo" id="txtTo" />
				<br/>
				<label class="eMessage" id="eTo"></label>
				</td>
			</tr>
			<?php
				}
			}
			?>
			<tr>
				<td></td>
				<td></td>
				<td><input name="btnSubmit" type="submit" class="input-submit" value="Save" /></td>
			</tr>
		</table>
</form>

</div>

<script type="text/javascript">

	function onTypeChange(obj)
	{
		jQuery('.type-rows').hide() ;
		jQuery('.clsType' + jQuery(obj).val()).show() ;
	}
	function doValidation()
	{
		var a = {
			'#txtTitle' :{ func : 'required()' , errfield : '#eTitle', errmsg  : 'Title not specified' },
			'#selType' :{ func : 'notvalue("0")' , errfield : '#eType', errmsg  : 'Type not selected' },
			<?php
			if( $classification != 'G' )
			{
			?>
			'#selBranch' :{ func : 'notvalue("0")' , errfield : '#eBranch', errmsg  : 'Branch not selected' },
			<?php
			}
			?>
		};
		if( validateForm(a, '#idErrorSummary' ) )
		{
			return true ;
		}
		return false;
	}

	/* submitForm( formName, beforeFunctionm, afterFunction, targetId, autofill json response); */
	submitForm( 'idFrmAdd', doValidation, function(data){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable'); setHightlightRow('<?php echo get_class($this);?>', data.__id ) ;}, 'idWorkArea', true);

	$("#txtFrom").datetimepicker({dateFormat: "dd-mm-yy", changeMonth: true,
		changeYear: true, timeFormat: "hh:mm:ss tt"});
	$("#txtTo").datetimepicker({dateFormat: "dd-mm-yy", changeMonth: true,
		changeYear: true, timeFormat: "hh:mm:ss tt"});
</script>