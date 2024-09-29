<div class='dms-popup' id='idDmsWindowPopup' >
	<div style="width: 100%;height: 20px;float: left;clear: both;">
		<span class="dms-popup-close" onclick="jQuery('#idDmsWindowPopup').popup('hide');"><img src="<?php echo baseUrl('assets/images/close.png'); ?>" /></span>
	</div>

	<input id="idWindowHidden" type="hidden" value="" />
	<h3 align="center">Window Edit</h3>
	<div style="clear: both"></div>
	<table>
		<tr>
			<td>Name</td>
			<td colspan="3" ><input type="text" id='idName' value="" style="width: 200px;" /></td>
		</tr>
		<tr>
			<td>Left</td>
			<td><input type="text" id='idLeft' value="" />px</td>
			<td>Top</td>
			<td><input type="text" id='idTop' value="" />px</td>
		</tr>
		<tr>
			<td>Width</td>
			<td><input type="text" id='idWidth' value="" />px</td>
			<td>Height</td>
			<td><input type="text" id='idHeight' value="" />px</td>
		</tr>
		<tr>
			<td>Font</td>
			<td>

				<select id="idFontFamily">
					<option value=""><?php echo QC_STR_SELECT;?></option>
					<?php
					foreach( $fonts as $one )
					{
					?>
					<option style="font-family: <?php echo $one['font'];?>" value="<?php echo $one['font'];?>"><?php echo $one['title'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>Font Size </td>
			<td><input type="text" id='idFontSize'  value="" />px</td>
		</tr>
		<tr>
			<td>Font Color</td>
				<td><input type="text" id='idColor' value="" style="width: 50px" /></td>
			    <td>Font Style</td>
				<td colspan="6">
				<div class="onoffswitch" style="float: right;">
					<input type="checkbox" class="onoffswitch-checkbox" id='idBold' value="bold" />
					<label class="onoffswitch-label" for="idBold"><b>B</b></label>
					<input type="checkbox" class="onoffswitch-checkbox" id='idItalic' value="italic" />
					<label class="onoffswitch-label" for="idItalic"><i>I</i></label>
					<input type="checkbox" class="onoffswitch-checkbox" id='idUnderline' value="underline" />
					<label class="onoffswitch-label" for="idUnderline"><u>U</u></label>
				</div>


			</td>

		</tr>
		<?php

		?>
		<tr>
			<td>Background Color</td>
			<td>
				<input style="float: left;" type="text" id='idBackground' value="" /></td>
			<td>Back Image</td>
			<td>
					<form style="float: left;" id="idFrameWrap" action="<?php echo siteUrl('canvas/upload');?>" method="POST" enctype='multipart/form-data' >
						<label class="upload-btn">
							<input type="file" onchange="jQuery(this).submit();" name="filWindowBackground" />
							<span>Browse Image</span>
						</label>
					</form>
				</div>

			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="button" value="Save" onclick="onWindowSettingsSubmit();jQuery('#idDmsWindowPopup').popup('hide');" /></td>
			<td><input type="button" value="Cancel" onclick="jQuery('#idDmsWindowPopup').popup('hide');" /></td>
		</tr>
	</table>
</div>

<script>
	/* submitForm( formName, beforeFunctionm, afterFunction, targetId, autofill json response); */
	submitForm( 'idFrameWrap', null, function(data){updateBackgroundString('#idBackground', data);}, '', true);

</script>