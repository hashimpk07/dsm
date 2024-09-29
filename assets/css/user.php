<?php

if( ! function_exists('qf_adjust_colors') )
{
	function qf_adjust_colors( $bg, $adjustment  )
	{
		$_int_1 = hexdec($bg[0] . $bg[1]) ;
		$_int_2 = hexdec($bg[2] . $bg[3]) ;
		$_int_3 = hexdec($bg[4] . $bg[5]) ;

		if( $adjustment > 0 )
		{
			if( $_int_1 > (0+$adjustment) || $_int_2 > (0+$adjustment) || $_int_3 > (0+$adjustment) )
			{
				$_int_1 -= $adjustment ;
				$_int_2 -= $adjustment ;
				$_int_3 -= $adjustment ;

				if( $_int_1 < 0 )
				{
					$_int_1 =0 ;
				}
				if( $_int_2 < 0 )
				{
					$_int_2 =0 ;
				}
				if( $_int_3 < 0 )
				{
					$_int_3 =0 ;
				}
			}
			else
			{
				$_int_1 += $adjustment ;
				$_int_2 += $adjustment ;
				$_int_3 += $adjustment ;

				if( $_int_1 > 255 )
				{
					$_int_1 = 255 ;
				}
				if( $_int_2 > 255 )
				{
					$_int_2 = 255 ;
				}
				if( $_int_3 > 255 )
				{
					$_int_3 = 255 ;
				}
			}
		}
		else
		{
			$adjustment = 0 - $adjustment ;
			if( $_int_1 < (255 - $adjustment) || $_int_2 < (255 - $adjustment) || $_int_3 < (255 - $adjustment) )
			{
				$_int_1 += $adjustment ;
				$_int_2 += $adjustment ;
				$_int_3 += $adjustment ;

				if( $_int_1 > 255 )
				{
					$_int_1 = 255 ;
				}
				if( $_int_2 > 255 )
				{
					$_int_2 = 255 ;
				}
				if( $_int_3 > 255 )
				{
					$_int_3 = 255 ;
				}
			}
			else
			{
				$_int_1 -= $adjustment ;
				$_int_2 -= $adjustment ;
				$_int_3 -= $adjustment ;

				if( $_int_1 < 0 )
				{
					$_int_1 = 0 ;
				}
				if( $_int_2 < 0 )
				{
					$_int_2 = 0 ;
				}
				if( $_int_3 < 0 )
				{
					$_int_3 = 0 ;
				}
			}
		}
		
		return ( str_pad(dechex($_int_1),2,'0', STR_PAD_LEFT)  . str_pad(dechex($_int_2),2,'0', STR_PAD_LEFT)  . str_pad(dechex($_int_3),2,'0', STR_PAD_LEFT) ) ;
	}
}
$bg = ((defined('PRM_THEME_BG')) ? @PRM_THEME_BG : '777777') ;


$bg_menu = $bg ;
$bg_select = qf_adjust_colors($bg, -15) ;
$bg_btn_select = qf_adjust_colors($bg, 30) ;

$bg_button = $bg ;

//echo '<br/>' ;
//echo $bg ;
//echo '<br/>' ;
//echo $bg_select ;
//echo '<br/>' ;
//echo $bg_button ;
//echo '<br/>' ;
//echo $bg_btn_select ;die;
?>
#idContentAreaMenu
{
background: #<?php echo $bg_menu ;?> !important;
}
#idContentAreaMenuHightlight,
#idContentAreaMenu .pure-menu a:hover,
#idContentAreaMenu .pure-menu a:focus,
#idContentAreaMenu .pure-menu a.selected
{
background: #<?php echo $bg_select ;?> !important;
}
button, html input[type="button"], input[type="reset"], input[type="submit"]
{
background: #<?php echo $bg_button ;?> ;
}
button:hover, html input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover
{
background: #<?php echo $bg_btn_select ;?> !important;
}