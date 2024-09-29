<div class="dvPageHeadingArea">
	<span class="heading"><?php echo ( ($mode =='edit') ? 'Edit' : 'Add');?> Item</span>

	<div class="subheading">
		<div class="bulkactions">
			<span title="List" class="flaticon flaticon-list" onclick="onListIconClick('idListArea<?php echo get_class($this);?>','<?php echo siteUrl('item/page'); ?>', {}, 'idContentAreaDetect')" ></span>
		</div>
	</div>
</div>

<div class="viewgrpwrap">

<form action="<?php echo @$url;?>" method="post" id="idFrmAdd<?php echo get_class($this);?>" >
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
				<td>Category <span class="red">*</span> </td>
				<?php //print_r($category);?>
					<td>:</td>
					<td><select  name="cbCategory" id="cbCategory">
						<option value='0'>--Select--</option>
					<?php
						//rewrite cateogory list..
						$colMap = array(
							'category_id' => 'id',
							'name' => 'name',
							
						);
						$catTree = onePassAdjacencyTree($category, $colMap) ;

						foreach( $catTree as $k => $v )
						{
							//echo '<optgroup label="' . $v['cat_name'] . '">' ;
							//if( isset($v['children']) )
						//	{
								//foreach ($v['children'] as $kc => $vc )
								//{
									$sel = '' ;
									if( $k == @$result['category_id'])
									{
										$sel = 'selected="selected" ' ;
									}
									?>
									<option <?php echo $sel;?> value="<?php echo $k ;?>" ><?php echo $v['name'];?></option>
									<?php
								//}
							//}
							//echo '</optgroup>' ;
						}
						?>
					</select>
				<br/>
				<label class="eMessage" id="eCategory"></label>
				</td>
			</tr>
			
			
			<tr>
				<td>Item Type <span class="red"> *</span></td>
					<td>:</td>
					<td>
						<?php
					$consumeable = '' ;
					$assets = '' ;
					if( @$result['type'] == 'C' )
					{
						$consumeable = 'checked="checked"' ;
					}
					else if(@$result['type'] == 'A')
					{
						$assets = 'checked="checked"' ;
					}
					?>
				    <input type="radio" name="txtConsume" value="A" class="radioConsume" <?php echo $assets ;?>  /> &nbsp;Asset
					<input type="radio" name="txtConsume"  value="C" class="radioConsume" <?php echo $consumeable ;?>  /> &nbsp;Consumable
					<br/>
				<label class="eMessage" id="eConsume"></label>
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
			'.radioConsume':{ func : 'ischecked()' , errfield : '#eConsume', errmsg  : 'Item type not selected' },
			'#cbCategory':{ func : 'notvalue("0")' , errfield : '#eCategory', errmsg  : 'Category not selected' }
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