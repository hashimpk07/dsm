<div id="idDmsScreen" class="dms-screen">
	
</div>

<script type="text/javascript">
	function hasWindow(data)
	{
		if( jQuery('#idDmsWindow_' + data.window_id).length > 0 )
		{
			return true ;
		}
		return false ;
	}
	function removeWindow(data)
	{
		jQuery('#' + data.window_id).remove() ;
	}
	function createWindow(data)
	{
		var style = "width:" + data.window_w + data.screen_unit + "; height:" + data.window_h + data.screen_unit + "; left:" + data.window_x + data.screen_unit + "; top:" + data.window_y + data.screen_unit + ";" ;
		var content = '' ;
		switch( data.window_type )
		{
			//image window
			case 'IU' :
				content = '<img data-checksum="' + data.window_checksum + '" class="dms-image dms-item data-checksum" src="' + data.window_data + '" />' ;
				break ;
			case 'I' :
				content = '<img data-checksum="' + data.window_checksum + '" class="dms-image dms-item data-checksum" src="' + QFS_SITE_URL + '/app/data/' + data.window_data + '" />' ;
				break ;
			//video window
			case 'V' :
				content = '<video data-checksum="' + data.window_checksum + '" class="dms-video dms-item data-checksum" width="100%" height="100%" src="' + QFS_SITE_URL + '/app/data/' + data.window_data + '" autoplay ></video>' ;
				break ;
			case 'VU' :
				content = '<video data-checksum="' + data.window_checksum + '" class="dms-video dms-item data-checksum" width="100%" height="100%" src="' + data.window_data + '" autoplay ></video>' ;
				break ;
			//text window
			case 'T' :
				content = '<div data-checksum="' + data.window_checksum + '" class="dms-text dms-item data-checksum">' + data.window_data + '</div>' ;
				break ;
			//html window
			case 'H' :
				content = '<div data-checksum="' + data.window_checksum + '" class="dms-html dms-item data-checksum">' + data.window_data + '</div>' ;
				break ;
			//scrolling window
			case 'S' :
				content = '<marquee><div data-checksum="' + data.window_checksum + '" class="dms-html dms-item data-checksum">' + data.window_data + '</div></marquee>' ;
				break ;
		}
		var html = '<div id="idDmsWindow_' + data.window_id + '" class="dms-window data-checksum" style="' + style + '" >' + content + '</div>' ;
		
		jQuery('#idDmsScreen').append(html) ;
	}
	function updateWindow(data)
	{
		jQuery('#idDmsWindow_' + data.window_id).css('width', data.window_w + data.screen_unit) ;
		jQuery('#idDmsWindow_' + data.window_id).css('height', data.window_h + data.screen_unit) ;
		jQuery('#idDmsWindow_' + data.window_id).css('left', data.window_x + data.screen_unit) ;
		jQuery('#idDmsWindow_' + data.window_id).css('top', data.window_y + data.screen_unit) ;
		
		if( jQuery( '.dms-item', jQuery('#idDmsWindow_' + data.window_id ) ).attr('data-checksum') !== data.window_checksum )
		{
			switch( data.window_type )
			{
				//image window
				case 'I' :
						jQuery( '.dms-image', jQuery('#idDmsWindow_' + data.window_id )).attr('src', data.window_data).attr('data-checksum', data.window_checksum) ;
					break ;
				//video window
				case 'V' :
					jQuery( '.dms-video', jQuery('#idDmsWindow_' + data.window_id )).attr('src', data.window_data).attr('data-checksum', data.window_checksum) ;
					break ;
				//text window
				case 'T' :
					jQuery( '.dms-text', jQuery('#idDmsWindow_' + data.window_id )).html(data.window_data).attr('data-checksum', data.window_checksum) ;
					break ;
				//html window
				case 'H' :
					jQuery( '.dms-html', jQuery('#idDmsWindow_' + data.window_id )).html(data.window_data).attr('data-checksum', data.window_checksum) ;
					break ;
			}
		}
	}
	function updateScreen(data)
	{
		jQuery('#idDmsScreen').css('width', data.screen_w + data.screen_unit ) ;
		jQuery('#idDmsScreen').css('height', data.screen_h + data.screen_unit) ;
	}
	function processData(data)
	{
		updateScreen(data) ;
		
		if( hasWindow(data) )
		{
			updateWindow(data) ;
		}
		else
		{
			removeWindow(data) ;
			createWindow(data) ;
		}
	}
	function processCommand(el)
	{
		if( el.command != 'undefined' )
		{
			switch()
			{
				case 'REFRESH' ;
						window.location.href = window.location.href ;
					break ;
			}
			return true ;
		}
		return false ;
	}
	setInterval(function(){
		getData( '<?php echo siteUrl('display/query/1');?>', {}, null, null, function(data) {
			jQuery.each(data, function(i,el)
			{
				if( ! processCommand(el) )
				{
					processData(el) ;
				}
			}) ;
		} ) ;
	}, 5000) ;
</script>