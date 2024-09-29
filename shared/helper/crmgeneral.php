<?php

	function addButton($url, $params, $targetId, $return = false, $caption = 'Add')
	{
		return createButton('flaticon-add', $caption, $url, $params, $targetId, $return   ) ;
	}
	function editButton($url, $params, $targetId, $return = false, $caption = 'Edit')
	{
		return createButton('flaticon-edit', $caption, $url, $params, $targetId, $return) ;
	}
	function closeButton($url, $params, $targetId, $return = false, $caption = '')
	{
		return createJButton('flaticon-close', '', $url, $params, $targetId, $return) ;
	}
	function deleteButton($url, $params, $targetId, $return = false, $caption = 'Delete')
	{
		return createButton('flaticon-trash', $caption, $url, $params, $targetId, $return) ;
	}
	function createButton($cls, $text, $url, $params, $targetId, $return = false)
	{
		$paramsz = '{}' ;
		if( is_array($params) )
		{
			if( count($params) > 0 )
			{
				$paramsz = json_encode($params) ; 
			}
		}
		
		ob_start() ;
		?>
<span title="Add" class="ce-action-button" style="cursor: pointer; line-height: 24px;float: right; margin-right: 5px;cursor: pointer;" onclick="actionView( '<?php echo $url ?>', <?php echo $paramsz ;?>, '<?php echo $targetId;?>');">
				<label style=" cursor: pointer; vertical-align: middle; line-height: 24px; font-size: 16px;">
					<span title="<?php echo $text;?>" class="<?php echo $cls;?>"></span>
				</label>
			</span>
		<?php
		$code = ob_get_clean() ;
		if( ! $return )
		{
			echo $code ;
		}
		return $code ;
	}
	function drawButton($img, $text, $event, $return = false)
	{
		ob_start() ;
		?>
		<div class="crmbutton" style="float: right; ">
			<span style="cursor: pointer; margin: 3px; line-height: 31px;float: right; cursor: pointer;" <?php echo $event ;?> >
				<label style=" cursor: pointer; vertical-align: middle; line-height: 24px; font-size: 16px;"><?php echo ucfirst($text) ; ?></label>
				<?php
				if( $img )
				{
				?>
				<img style="vertical-align: middle;" src="<?php echo baseUrl();?>assets/images/<?php echo $img;?>" />
				<?php
				}
				?>
			</span>
		</div>
		<?php
		$code = ob_get_clean() ;
		if( ! $return )
		{
			echo $code ;
		}
		return $code ;
	}
	function createJButton($img, $text, $url, $params, $targetId, $return = false)
	{
		if( !is_array($params) )
		{
			$params = array() ;
		}
		ob_start() ;
		?>
		<div style="width: 100%;float: left;">
			<span onclick="<?php echo $url;?>"  style="padding: 2px 8px; cursor: pointer; margin: 3px; line-height: 24px;float: right; cursor: pointer;" onclick="actionView('<?php echo $url ?>', <?php echo json_encode($params);?>, '<?php echo $targetId;?> ');">
				<span style="font-size: 16px;cursor: pointer;" style="cursor: pointer;"><?php echo ucfirst($text);?></span>
				<img style="vertical-align: middle;" src="<?php echo baseUrl();?>assets/images/<?php echo $img;?>" />
			</span>
		</div>
		<?php
		$code = ob_get_clean() ;
		if( ! $return )
		{
			echo $code ;
		}
		return $code ;
	}
	function boxOpen()
	{
		?>
		<div style="float: left;width: 100%; margin : 10px 0; ">
		<div style="float: right;">
		<?php closeButton("javascript:jQuery('#idWorkArea').slideUp('fast');", null, '') ; ?>
		</div>
	<?php
	}
	function boxClose()
	{
		echo '</div>' ;
	}
	function tinyMce($class, $includeJs = true)
	{
		if( $includeJs )
		{
		?>
		<!-- TinyMCE -->
		<script type="text/javascript" src="<?php echo baseUrl(); ?>assets/packages/tinymce/tiny_mce.js"></script>
		<?php
		}
		?>
<script type="text/javascript">

	// O2k7 skin (silver)
	tinyMCE.init({
		// General options
		mode : "specific_textareas",
		elements : "abshosturls",
		editor_selector : "<?php echo $class;?>",
		theme : "advanced",
		skin : "o2k7",
		skin_variant : "silver",
		plugins : "advimage, jbimages",
		relative_urls : false,
        remove_script_host : false,
		theme_advanced_resizing : true,
        image_advtab: true,
        
		//use_native_selects: true,
//		style_formats: [
//			{title: 'Bold text', inline: 'b', styles : {fontSize: '14px'}},
//			{title: 'Italic text', inline: 'em', styles: {fontSize: '14px'}},
//			{title: 'Underlined text', inline: 'u', styles: {fontSize: '14px'}},
//			{title: 'Serif Heading', block: 'h1', styles: {fontFamily: 'serif'}},
//			{title: 'Sans Serif Heading', block: 'h1', styles: {color: 'sans serif'}},
//			{title: 'Monotype Heading', block: 'h1', styles: {color: 'monotype'}},
//		],

//		// Theme options
		theme_advanced_buttons1 : " newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect", //styleselect,
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image, jbimages,cleanup, help,code,|,forecolor,backcolor",
		theme_advanced_buttons3 : "hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions",
		//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
//		theme_advanced_toolbar_location : "top",
//		theme_advanced_toolbar_align : "left",
//		theme_advanced_statusbar_location : "bottom",
////		theme_advanced_resizing : true,
	});
</script>
	<?php
	}
?>