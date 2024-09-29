<div class="idvPageHeadingArea">
	<h1>Screen Edit</h1>
</div>
<div class="canvas-palette">

	<!-- Add Window Button -->
	<a href="javascript:void(0)" class="pal-control" style="line-height: 30px" onclick="newWindow()" > Add Window</a>
	<!-- Zoom Control -->
	<div class="pal-control rngZoomContainer">
		<input class="rngZoom" type="range" min="5" value="10" max="15" step="5" onchange="scaleScreen(this)" />
		<div class="rngZoomPointer">
			<span>-</span>
			<span>0</span>
			<span>+</span>
		</div>
	</div>

	<!-- Size Box-->
	<div class="pal-control" >
		<input type="text" placeholder="Width" name="txtWidth" id="idScreenWidth" value="<?php echo $screen['w'];?>" style="width: 60px;" onchange="sizeScreen()" />
		<input type="text" placeholder="Height" name="txtHeight" id="idScreenHeight" value="<?php echo $screen['h'];?>" style="width: 60px;" onchange="sizeScreen()" />
	</div>

	<!-- Form Box -->


	<form style="float: right;" class="pal-control" action="<?php echo @$url;?>" method="post" onsubmit="beforeSubmit()" id="idFrmScreen<?php echo get_class($this);?>" >
		<div id="idScreenData"></div>
		<input type="text" style="width: 50px" title="Screen Background" placeholder="Color" name="txtScreenBackground" class="clsScreenBackground" id="idScreenBackground" value="<?php echo @$screen['background'];?>" onchange="updateBackground()" />
		<input type="text" placeholder="Screen Name" title="Screen Name" name="txtScreenName" value="<?php echo @$screen['name'];?>"/>

			<input type="hidden" name="editId" value="<?php echo $screen['id'];?>" />
			<input name="btnApply" type="submit" class="input-submit" value="Save" />
			<input name="btnSubmit" type="submit" class="input-submit" value="Save &amp; Close" />
			<input name="btnCancel" onclick="actionView('/dms2/index.php/screen/page', {}, 'idContentAreaSmall');" type="submit" class="input-submit" value="Cancel" />
	</form>

			<form style="float: left;" id="idScreenFrameWrap" action="<?php echo siteUrl('canvas/upload');?>" method="POST" enctype='multipart/form-data' >
			<label class="upload-btn">
				<input type="file" onchange="jQuery(this).submit();" name="filWindowBackground" />
				<span>Browse Image</span>
			</label>
		</form>

		<script>
		submitForm( 'idScreenFrameWrap', null, function(data){updateBackgroundString('.clsScreenBackground', data); jQuery('#idDmsScreen').css('background', jQuery('.clsScreenBackground').val() );}, '', true);
		</script>

</div>