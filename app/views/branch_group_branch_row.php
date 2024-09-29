<?php
foreach( $branches AS $one ) {
	$sel = '' ;
	
	if(is_array(@$sel_branches) )
	{
		if( in_array($one['id'], $sel_branches) )
		{
			$sel = ' checked="checked" ' ;
		}
	}
?>
<br/>
<input <?php echo $sel;?> type="checkbox" name="<?php echo urldecode($field_name);?>" value="<?php echo $one['id'];?>" /> <?php echo $one['name']; ?>
<?php
}