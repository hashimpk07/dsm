<div id="idDmsScreen" class="dms-screen">
	
</div>

<input type="hidden" id="idScreenId" value="<?php echo $screen_id;?>" />
<input type="hidden" id="idBranchId" value="<?php echo $branch_id;?>" />

<script type="text/javascript">
	function hasWindow(data)
	{
		var ret = false ;
		if( jQuery('#idDmsWindow_' + data.window_id).length > 0 )
		{
			ret = true ;
			jQuery.each(data.window_data, function(x, ydata)
			{
				switch( data.window_type )
				{
				//image window
				case 'IU' :
					if( jQuery( '#idDmsWindow_' + data.window_id + ' .dms-imageurl.dms-lang' + ydata.lang_id ).length < 1 ) 
					{
						ret = false ;
					}
					break ;
				case 'I' :
					if( jQuery( '#idDmsWindow_' + data.window_id + ' .dms-image.dms-lang' + ydata.lang_id ).length < 1 ) 
					{
						ret = false ;
					}
					break ;
				//video window
				case 'V' :
					if( jQuery( '#idDmsWindow_' + data.window_id + ' .dms-video.dms-lang' + ydata.lang_id ).length < 1 ) 
					{
						ret = false ;
					}
					break ;
				case 'VU' :
					if( jQuery( '#idDmsWindow_' + data.window_id + ' .dms-videourl.dms-lang' + ydata.lang_id ).length < 1 ) 
					{
						ret = false ;
					}
					break ;
				//text window
				case 'T' :
					if( jQuery( '#idDmsWindow_' + data.window_id + ' .dms-text.dms-lang' + ydata.lang_id ).length < 1 ) 
					{
						ret = false ;
					}
					break ;
				//html window
				case 'H' :;
					if( jQuery( '#idDmsWindow_' + data.window_id + ' .dms-html.dms-lang' + ydata.lang_id ).length < 1 ) 
					{
						ret = false ;
					}
					break ;
				//scrolling window
				case 'S' :
					if( jQuery( '#idDmsWindow_' + data.window_id + ' .dms-scroll.dms-lang' + ydata.lang_id ).length < 1 ) 
					{
						ret = false ;
					}
					break ;
			}
			}) ;
		}
		return ret ;
	}
	function removeWindow(wid)
	{
		jQuery('#idDmsWindow_' + wid).remove() ;
	}
	function createWindow(data, screen)
	{
		var style = "width:" + data.window_w + screen.screen_unit + "; height:" + data.window_h + screen.screen_unit + "; left:" + data.window_x + screen.screen_unit + 
				"; top:" + data.window_y + screen.screen_unit + ";background:" + data.window_background + ';color:' + data.window_text_color + ';' +
				"; font-size:" + data.window_font_size + '; font-family:' + data.window_font_family + ';' +
				"; font-weight:" + data.window_font_weight + '; font-style:' + data.window_font_style + ';' +
				"; text-decoration:" + data.window_text_decoration + ';' ;
		var content = '' ;

		jQuery.each(data.window_data, function(x, ydata)
		{
			switch( data.window_type )
			{
			//image window
			case 'IU' :
				content += '<img data-checksum="' + ydata.checksum + '" class="dms-imageurl dms-item dms-lang' + ydata.lang_id + ' data-checksum" src="' + ydata.data + '" />' ;
				break ;
			case 'I' :
				content += '<img data-checksum="' + ydata.checksum + '" class="dms-image dms-item dms-lang' + ydata.lang_id + ' data-checksum" src="' + ydata.data + '" />' ;
				break ;
			//video window
			case 'V' :
				content += '<video data-checksum="' + ydata.checksum + '" class="dms-video dms-item dms-lang' + ydata.lang_id + ' data-checksum" width="100%" height="100%" src="' + ydata.data + '" autoplay loop ></video>' ;
				break ;
			case 'VU' :
				content += '<video data-checksum="' + ydata.checksum + '" class="dms-videourl dms-item dms-lang' + ydata.lang_id + ' data-checksum" width="100%" height="100%" src="' + ydata.data + '" autoplay loop ></video>' ;
				break ;
			//text window
			case 'T' :
				var s = (( ydata.dir == 'r' ) ? 'direction:rtl;' : '' ) ;
				s += (( ydata.font != '' ) ? 'font-family:' + ydata.font : '' ) ;
				s = ' style="' + s + '" ' ;
				content += '<div ' + s + ' data-checksum="' + ydata.checksum + '" class="dms-text dms-item dms-lang' + ydata.lang_id + ' data-checksum">' + ydata.data + '</div>' ;
				break ;
			//html window
			case 'H' :
				var s = (( ydata.dir == 'r' ) ? 'direction:rtl;' : '' ) ;
				s += (( ydata.font != '' ) ? 'font-family:' + ydata.font : '' ) ;
				s = ' style="' + s + '" ' ;
				content += '<div ' + s + ' data-checksum="' + ydata.checksum + '" class="dms-html dms-item dms-lang' + ydata.lang_id + ' data-checksum">' + ydata.data + '</div>' ;
				break ;
			//scrolling window
			case 'S' :
				var d = (( ydata.dir == 'r' ) ? ' direction="right" ' : '' ) ;
				var s = (( ydata.font != '' ) ? 'font-family:' + ydata.font : '' ) ;
				s = 'style="' + s + '"' ;
				content += '<marquee ' + s + d + ' ><div data-checksum="' + ydata.checksum + '" class="dms-scroll dms-item dms-lang' + ydata.lang_id + ' data-checksum">' + ydata.data + '</div></marquee>' ;
				break ;
			}
		}) ;
		var html = '<div data-wid="' + data.window_id + '" id="idDmsWindow_' + data.window_id + '" class="dms-window data-checksum" style="' + style + '" >' + content + '</div>' ;
		
		jQuery('#idDmsScreen').append(html) ;
	}
	function removeUnwanted(dataset)
	{
		var alive = false ;

		jQuery('.dms-window').each( function(x, w){
			alive = false ;
			jQuery.each(dataset.windows, function(i,el)
			{
				if( jQuery(w).attr('data-wid') === el.window_id )
				{
					alive = true ;
				}
			});
			
			if( ! alive )
			{
				jQuery(w).remove() ;
			}

		}) ;
	}
	function updateWindow(data, screen)
	{
		jQuery('#idDmsWindow_' + data.window_id).css('width', data.window_w + screen.screen_unit) ;
		jQuery('#idDmsWindow_' + data.window_id).css('height', data.window_h + screen.screen_unit) ;
		jQuery('#idDmsWindow_' + data.window_id).css('left', data.window_x + screen.screen_unit) ;
		jQuery('#idDmsWindow_' + data.window_id).css('top', data.window_y + screen.screen_unit) ;
		jQuery('#idDmsWindow_' + data.window_id).css('background', data.window_background) ;
		jQuery('#idDmsWindow_' + data.window_id).css('color', data.window_text_color) ;
		jQuery('#idDmsWindow_' + data.window_id).css('font-size', data.window_font_size) ;
		jQuery('#idDmsWindow_' + data.window_id).css('font-family', data.window_font_family) ;
		
		jQuery('#idDmsWindow_' + data.window_id).css('font-weight', data.window_font_weight) ;
		jQuery('#idDmsWindow_' + data.window_id).css('font-style', data.window_font_style) ;
		jQuery('#idDmsWindow_' + data.window_id).css('text-decoration', data.window_text_decoration) ;
		
		jQuery.each(data.window_data, function(x, ydata)
		{
			if( jQuery( '.dms-item.dms-lang' + ydata.lang_id, jQuery('#idDmsWindow_' + data.window_id ) ).attr('data-checksum') !== ydata.checksum )
			{
				switch( data.window_type )
				{
					//image window
					case 'I' :
							jQuery( '.dms-image.dms-lang' + ydata.lang_id , jQuery('#idDmsWindow_' + data.window_id )).attr('src', ydata.data).attr('data-checksum', ydata.checksum) ;
						break ;
					//image url
					case 'IU' :
							jQuery( '.dms-imageurl.dms-lang' + ydata.lang_id , jQuery('#idDmsWindow_' + data.window_id )).attr('src', ydata.data).attr('data-checksum', ydata.checksum) ;
						break ;
					//video window
					case 'V' :
						jQuery( '.dms-video.dms-lang' + ydata.lang_id, jQuery('#idDmsWindow_' + data.window_id )).attr('src', ydata.data).attr('data-checksum', ydata.checksum) ;
						break ;
					//video url
					case 'VU' :
						jQuery( '.dms-videourl.dms-lang' + ydata.lang_id, jQuery('#idDmsWindow_' + data.window_id )).attr('src', ydata.data).attr('data-checksum', ydata.checksum) ;
						break ;
					//text window
					case 'T' :
						jQuery( '.dms-text.dms-lang' + ydata.lang_id, jQuery('#idDmsWindow_' + data.window_id )).html(ydata.data).attr('data-checksum', ydata.checksum) ;
						break ;
					//Scrolling text
					case 'S' :
						jQuery( '.dms-scroll.dms-lang' + ydata.lang_id, jQuery('#idDmsWindow_' + data.window_id )).html(ydata.data).attr('data-checksum', ydata.checksum) ;
						break ;
					//html window
					case 'H' :
						jQuery( '.dms-html.dms-lang' + ydata.lang_id, jQuery('#idDmsWindow_' + data.window_id )).html(ydata.data).attr('data-checksum', ydata.checksum) ;
						break ;
				}
			}
		}) ;
	}
	function updateScreen(screen)
	{
		jQuery('#idDmsScreen').css('width', screen.screen_w + screen.screen_unit ) ;
		jQuery('#idDmsScreen').css('height', screen.screen_h + screen.screen_unit) ;
		jQuery('body').css('background', screen.screen_background) ;
		
		//IF SCREEN CHANGED? REFRESH
		if( jQuery('#idScreenId').val() != screen.screen_id )
		{
			jQuery('#idDmsScreen').html('') ;
		}
	}
	function processWindow(data, screen)
	{
		if( hasWindow(data) )
		{
			updateWindow(data, screen) ;
		}
		else
		{
			removeWindow(data.window_id) ;
			createWindow(data, screen) ;
		}
	}
	function processCommand(el)
	{
		ret = false ;
//		//IF SCREEN CHANGED? REFRESH
//		if( jQuery('#idScreenId').val() != el.screen_id )
//		{
//			el.command = 'REFRESH' ;//TODO; try it with out refresh... try entire clean up..
//		}

		if( typeof el.command != 'undefined' )
		{
			switch(el.command)
			{
				case 'REFRESH' :
						window.location.href = window.location.href ;
						ret = true ;
					break ;
			}
		}
		return ret ;
	}
	setInterval(function(){
		getData( '<?php echo siteUrl('display/query/' );?>', {'branchId' : jQuery('#idBranchId').val() }, null, null, function(data) {
			
			removeUnwanted(data) ;
			
			updateScreen(data) ; //passing screen data.
			processCommand(data) ;
			
			jQuery.each(data.windows, function(i,window)
			{
				processWindow(window, data) ;
			}) ;
		} ) ;
	}, <?php echo intval(PRM_REQUEST_INTERVAL * 1000);?>) ;
</script>

<?php /* CASHING */ ?>

<?php if( PRM_ENABLE_CACHE ) { ?>
<iframe id="idCacheFrame" src="" width="100" height="100" style="display: none;" ></iframe>
<script>
	var src = '<?php echo siteUrl('display/query_cache/' . $branch_id );?>' ;
	setInterval(function(){
		var srcNoCache = src + '?' + Math.random() ;

		jQuery('#idCacheFrame').attr( 'src', srcNoCache ) ;
		
	}, <?php echo intval(PRM_PRELOAD_TIME * 1000);?>) ;
</script>
<?php
}
?>