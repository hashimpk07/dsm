<div id='idDmsScreen' class='dms-screen' style="background: <?php echo $screen['background'];?>; height: <?php echo $screen['h'] . QC_SCREEN_UNIT ;?>; width: <?php echo $screen['w']. QC_SCREEN_UNIT ;?>; overflow: hidden;">
</div>

<?php
include 'canvas_window_popup.php' ;
?>

<?php
/*
if( QC_SCREEN_UNIT == '%' )
{
?>
<style>
body, html, #idDmsCanvas, #idContentAreaSmall, #layout
{
	height: 100%;
	width: 100%;
}
</style>
<?php
}
 */
?>
<script>
		//Add events for screen
	jQuery("#idDmsScreen").resizable({
			stop: function( event, ui ) { onStopWindowResize(this,ui);}
		  });

	//for window setings
	jQuery("#idDmsWindowPopup").draggable();

	jQuery('#idScreenBackground').ColorPicker({
			onBeforeShow: function() {
				$('#idScreenBackground').ColorPickerSetColor(this.value);
			},
			onChange: function(hsb, hex, rgb) {
				$('#idScreenBackground').val('#' + hex);
				$('#idDmsScreen').css('background', '#' + hex) ;
			}
		});

	function newWindow(data)
	{
		var obj = new Date() ;
		var n = "Window_" + obj.getFullYear() + '_' + obj.getMonth() + '_' + obj.getDay() + '_' + obj.getHours() + '_' + obj.getMinutes() + '_'+ obj.getSeconds() ;

		var id = 'idWindow_' + (Math.random().toString().replace('.','')) ;
		var settingsIcon = '<?php echo baseUrl('assets/images/settings20.png');?>' ;
		var closeIcon = '<?php echo baseUrl('assets/images/close20.png');?>' ;
		var w = '200px';
		var h = '200px';
		var l = '0px';
		var t = '0px';
		var s = '1em' ;
		var f = 'Sans-serif';
		var bg = '#FFF' ;
		var c = '#000' ;
		var wid = '' ;
		var sb = 'normal' ;
		var si = 'normal' ;
		var su = 'none' ;

		if( typeof data == 'object' )
		{
			var w = data.window_w + data.screen_unit ;
			var h = data.window_h + data.screen_unit ;
			var l = data.window_x + data.screen_unit ;
			var t = data.window_y + data.screen_unit ;
			var s = data.window_font_size ;
			var f = data.window_font_family ;
			var c = data.window_text_color ;
			var bg = data.window_background ;
			var wid = data.window_id ;
			var n = data.window_name ;
			var sb = data.window_font_weight ;
			var si = data.window_font_style ;
			var su = data.window_text_decoration;
		}

		var window_html = '<div class="dms-window" data-name="' + n + '" data-id="' + wid + '" id="' +  id +
				'" style="left:' + l +';top:' +t  +';font-family:' + f +';font-size:' + s +';color:' + c
				+ '; font-weight:' + sb + '; font-style:' + si + '; text-decoration:' + su
					+';width: ' + w + '; height: ' + h + '; background: ' + bg + ';" >\n\
				<img class="dms-toolbar-setting dms-toolbar-item" src="' + settingsIcon + '" onclick="showWindowSetings(\'' + id + '\')" />\n\
				<img class="dms-toolbar-close dms-toolbar-item" src="' + closeIcon + '" onclick="closeWindow(\'#' + id + '\')" /></div>' ;

		jQuery("#idDmsScreen").append(window_html);

		jQuery('#' + id ).draggable({
//revert: true,
			zIndex: 100,
			drag: function(event, ui) {
//resize bug fix ui drag `enter code here`
				__dx = ui.position.left - ui.originalPosition.left;
				__dy = ui.position.top - ui.originalPosition.top;
//ui.position.left = ui.originalPosition.left + ( __dx/__scale);
//ui.position.top = ui.originalPosition.top + ( __dy/__scale );
				ui.position.left = ui.originalPosition.left + (__dx);
				ui.position.top = ui.originalPosition.top + (__dy);
//
				ui.position.left += __recoupLeft;
				ui.position.top += __recoupTop;
			},
			start: function(event, ui) {
				$(this).css('cursor', 'pointer');
//resize bug fix ui drag
				var left = parseInt($(this).css('left'), 10);
				left = isNaN(left) ? 0 : left;
				var top = parseInt($(this).css('top'), 10);
				top = isNaN(top) ? 0 : top;
				__recoupLeft = left - ui.position.left;
				__recoupTop = top - ui.position.top;
			},
			create: function(event, ui) {
				$(this).attr('oriLeft', $(this).css('left'));
				$(this).attr('oriTop', $(this).css('top'));
			}
		}).resizable();
	}

<?php
foreach( @$windows as $rec )
{
$windowOne = array(
	'screen_unit' => QC_SCREEN_UNIT,
	'screen_w' => $rec['sw'] ,
	'screen_h' => $rec['sh']  ,
	'window_id' => $rec['window_id'],
	'window_x' => $rec['wx'] ,
	'window_y' => $rec['wy'] ,
	'window_w' => $rec['ww'] ,
	'window_h' => $rec['wh'] ,
	'window_background' => $rec['background'] ,
	'window_text_color' => $rec['text_color'] ,
	'window_font_family' => $rec['font_family'] ,
	'window_font_size' => $rec['font_size'] ,
	'window_font_weight' => $rec['font_weight'] ,
	'window_font_style' => $rec['font_style'] ,
	'window_text_decoration' => $rec['text_decoration'] ,
	'window_name' => $rec['window_name'] ,
);
	?>
	var data = <?php echo json_encode($windowOne) ; ?> ;

	newWindow(data) ;
	<?php
}
?>
</script>

<script type="text/javascript">
	function updateBackground()
	{
		var bg = jQuery('#idScreenBackground').val() ;
		jQuery('#idDmsScreen').css('background', bg) ;
	}
	function sizeScreen()
	{
		var w = jQuery('#idScreenWidth').val() ;
		var h = jQuery('#idScreenHeight').val() ;

		jQuery('#idDmsScreen').css('width', w) ;
		jQuery('#idDmsScreen').css('height', h) ;
	}

	function beforeSubmit()
	{
		var index = 0 ;

		jQuery('#idScreenData').empty() ;

		jQuery('.dms-window').each(function(i, j){

			index ++ ;

			var l = jQuery(this).css('left');
			var t = jQuery(this).css('top');
			var w = jQuery(this).css('width');
			var h = jQuery(this).css('height');
			var f = jQuery(this).css('font-family');
			var s = jQuery(this).css('font-size');
			var id = jQuery(this).attr('data-id');
			var n = jQuery(this).attr('data-name');
			var b = encodeURI(buildBackground( jQuery(this) )) ; //expecting quotes
			var c = rgb2hex(jQuery(this).css('color'));
			var sb = jQuery(this).css('font-weight');
			var si = jQuery(this).css('font-style');
			var su = jQuery(this).css('text-decoration');

			var pl = jQuery(this).parent().css('left');
			var pt = jQuery(this).parent().css('top');
			var pw = jQuery(this).parent().css('width');
			var ph = jQuery(this).parent().css('height');

			//DOM FIX
			if( sb == '700' )
			{
				sb = 'bold' ;
			}
			<?php /*
			if( QC_SCREEN_UNIT == '%' )
			{
			?>
						l = (l / pl) * 100 ;
						t = (t / pt) * 100 ;
						w = (w / pw) * 100 ;
						h = (h / ph) * 100 ;
			<?php
			}
			 */
			?>

			var str = '<input type="hidden" name="txtLeft[' + index + ']" value="' + l + '" />' ;
			str += '<input type="hidden" name="txtTop[' + index + ']" value="' + t + '" />' ;
			str += '<input type="hidden" name="txtWidth[' + index + ']" value="' + w + '" />' ;
			str += '<input type="hidden" name="txtHeight[' + index + ']" value="' + h + '" />' ;
			str += '<input type="hidden" name="txtFontFamily[' + index + ']" value="' + f + '" />' ;
			str += '<input type="hidden" name="txtFontSize[' + index + ']" value="' + s + '" />' ;
			str += '<input type="hidden" name="txtBackground[' + index + ']" value="' + b + '" />' ;
			str += '<input type="hidden" name="txtColor[' + index + ']" value="' + c + '" />' ;
			str += '<input type="hidden" name="txtId[' + index + ']" value="' + id + '" />' ;
			str += '<input type="hidden" name="txtName[' + index + ']" value="' + n + '" />' ;

			str += '<input type="hidden" name="txtBold[' + index + ']" value="' + sb + '" />' ;
			str += '<input type="hidden" name="txtItalic[' + index + ']" value="' + si + '" />' ;
			str += '<input type="hidden" name="txtUnderline[' + index + ']" value="' + su + '" />' ;

			jQuery('#idScreenData').append(str) ;
		}) ;
		//reset and add screen info
		var sw = jQuery('#idDmsScreen').css('width');
		var sh = jQuery('#idDmsScreen').css('height');
		var sb = jQuery('#idDmsScreen').css('background');

		str = '<input type="hidden" name="txtScreenWidth" value="' + sw + '" />' ;
		str += '<input type="hidden" name="txtScreenHeight" value="' + sh + '" />' ;
		str += '<input type="hidden" name="txtScreenBackground" value="' + sb + '" />' ;
		jQuery('#idScreenData').append(str) ;
		return true ;
	}


	/* submitForm( formName, beforeFunctionm, afterFunction, targetId, autofill json response); */
	submitForm( 'idFrmScreen<?php echo get_class($this);?>', null, function(data){}, 'idWorkArea', true);

</script>



<script>


	function onWindowSettingsSubmit()
	{
		var l = jQuery('#idLeft').val();
		var n = jQuery('#idName').val();
		var t = jQuery('#idTop').val();
		var w = jQuery('#idWidth').val();
		var h = jQuery('#idHeight').val();
		var f = jQuery('#idFontFamily').val();
		var s = jQuery('#idFontSize').val();
		var b = jQuery('#idBackground').val();
		var c = jQuery('#idColor').val();
		var sb = ((jQuery('#idBold').is(':checked')) ? 'bold' : 'normal') ;
		var si = ((jQuery('#idItalic').is(':checked')) ? 'italic' : 'normal') ;
		var su = ((jQuery('#idUnderline').is(':checked')) ? 'underline' : 'none') ;

		var id = jQuery('#idWindowHidden').val() ;

		jQuery('#' + id).css('left', l).css('top', t).css('width', w).css('height', h)
				.css('font-family', f).css('font-size', s)
				.css('background', b).css('color', c).attr('data-name', n)
				.css('font-weight', sb )
				.css('font-style', si)
				.css('text-decoration', su) ;

	}

	function buildBackground(object)
	{
		var c = jQuery(object).css('background-color') ;
		var i = jQuery(object).css('background-image') ;
		var r = jQuery(object).css('background-repeat') ;

		//color
		if( c.length > 0 && c.toLowerCase() !== 'transparent' )
		{
			c = rgb2hex(c);//NOTE; background-color was originally
		}
		else
		{
			c = '' ;
		}
		//image
		var bgstr = c + " " + i + " " + r ;
		return bgstr ;
	}
	function showWindowSetings(windowId)
	{
		var l = jQuery('#' + windowId).css('left');
		var n = jQuery('#' + windowId).attr('data-name');
		var t = jQuery('#' + windowId).css('top');
		var w = jQuery('#' + windowId).css('width');
		var h = jQuery('#' + windowId).css('height');
		var f = jQuery('#' + windowId).css('font-family');
		var s = jQuery('#' + windowId).css('font-size');
		var b = buildBackground('#' + windowId) ;
		var c = rgb2hex(jQuery('#' + windowId).css('color'));

		var sb = jQuery('#' + windowId).css('font-weight');
		var si = jQuery('#' + windowId).css('font-style');
		var su = jQuery('#' + windowId).css('text-decoration');

		if(  n )
		{
			obj = new Date();
			n = "Window_" + obj.getFullYear() + '_' + obj.getMonth() + '_' + obj.getDay() + '_' + obj.getHours() + '_' + obj.getMinutes() + '_'+ obj.getSeconds() ;
		}
		jQuery('#idWindowHidden').val(windowId);

		l = l.split('px');
		t = t.split('px');
		w = w.split('px');
		h = h.split('px');
		s = s.split('px');

		jQuery('#idLeft').val(l[0]);
		jQuery('#idTop').val(t[0]);
		jQuery('#idWidth').val(w[0]);
		jQuery('#idHeight').val(h[0]);
		jQuery('#idFontFamily').val(f);
		jQuery('#idFontSize').val(s[0]);
		jQuery('#idName').val(n);
		jQuery('#idBackground').val(b);
		jQuery('#idColor').val(c);

		if( (sb == '700') || (sb == 'bold') ) { jQuery('#idBold').prop('checked', true) ; }
		else { jQuery('#idBold').removeAttr('checked') ; }
		if( si == 'italic' ) { jQuery('#idItalic').prop('checked', true) ; }
		else { jQuery('#idItalic').removeAttr('checked') ; }
		if( su == 'underline' ) { jQuery('#idUnderline').prop('checked', true) ; }
		else { jQuery('#idUnderline').removeAttr('checked') ; }


		$('#idBackground').ColorPicker({
			onBeforeShow: function() {
				$('#idBackground').ColorPickerSetColor(this.value);
			},
			onChange: function(hsb, hex, rgb) {
				$('#idBackground').val('#' + hex);
			}
		});
		$('#idColor').ColorPicker({
			onBeforeShow: function() {
				$('#idColor').ColorPickerSetColor(this.value);
			},
			onChange: function(hsb, hex, rgb) {
				$('#idColor').val('#' + hex);
			}
		});
		jQuery('#idDmsWindowPopup').popup('show');
	}

	function closeWindow(selector)
	{
		jQuery(selector).remove() ;
	}
	function scaleScreen(obj)
	{
		var val = jQuery(obj).val() / 10 ;
		jQuery('#idDmsScreen').css('-ms-transform', 'scale(' + val + ',' + val + ')') ;
		jQuery('#idDmsScreen').css('-webkit-transform', 'scale(' + val + ',' + val + ')') ;
		jQuery('#idDmsScreen').css('transform', 'scale(' + val + ',' + val + ')') ;
	}
	function onStopWindowResize(obj, ui)
	{
		var w = jQuery('#idDmsScreen').css('width') ;
		var h = jQuery('#idDmsScreen').css('height') ;

		jQuery('#idScreenWidth').val(w) ;
		jQuery('#idScreenHeight').val(h) ;
	}
</script>