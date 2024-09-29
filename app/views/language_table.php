<form action="<?php echo siteUrl('language/bulkaction');?>" method="post" id="frmList<?php echo get_class($this);?>" >
	<input type="hidden" id="hidbulkaction" name="hidbulkaction" value="" />
	<div class="list-iconset">
		<?php if($this->accessAllowed('language', 'delete') ) { ?>
		<span class="flaticon-trash def-flaticon" title="Delete" onclick="return onBulkActionClick('frmList<?php echo get_class($this);?>', 'delete', 'Delete selected Languages ?')" type="image" name="aclaction-delete" value="Delete Selected" id="idbulkdelete" />		
		<?php } ?>
	</div>

<table width="100%" style="float: left;" class="tab-grey">
	<tr>
		<th width="1"><input type="checkbox" class="listchk-all" /></th>
		<th width="1"><?php echo drawTh('#', 'id');?></th>
		<th class="m s" ><?php echo drawTh('Name', 'l.name');?></th>
		<th><?php echo drawTh('Font', 'l.font');?></th>
		<th><?php echo drawTh('Direction', 'l.dir');?></th>
		<th class="m action-column" >Actions</th>
	</tr>

	<?php
	$i = 0 ;
	if( is_array($records) )
	{
		if( count($records) > 0 )
		{
			foreach ($records as $rec)
			{
				
				$bg = ($i++ % 2) ? 'bg' : 'nobg' ;
				?>
				<tr class="<?php echo $bg; ?>" id="idTableRow<?php echo $rec['id'];?>">
					<td><input name="cbList[]" value="<?php echo @$rec['id']; ?>" type="checkbox" class="listchk-language" /></td>
					<td class="v"><?php echo @$rec['qslno']; ?></td>
					<td class="m s v"><?php echo @$rec['name']; ?></td>
					<td class="v"><?php echo @$rec['font']; ?></td>
					<td class="v"><?php echo ((@$rec['dir']=='r') ? 'Right To Left' : 'Left To Right') ; ?></td>
					<td class="m action-column">
						<?php if($this->accessAllowed('language', 'edit') ) { ?>
						<span class="flaticon-edit" title="<?php echo l('Edit details');?>" onclick="actionEdit( '<?php echo siteUrl('language/edit' . '/' . @$rec['id'] );?>', {}, 'idContentAreaDetect');" />
						<?php } ?>
						<?php if($this->accessAllowed('language', 'delete') ) { ?>
						<span class="flaticon flaticon-trash" title="<?php echo l('Delete');?>" onclick="actionFlag( '<?php echo siteUrl('language/delete' . '/' . @$rec['id'] );?>', {}, 'Are you sure you want to delete this language?', '', null, function(){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable')}, true);" />
						<?php } ?>
					</td>
					<script type="text/javascript">
						jQuery('#idTableRow<?php echo $rec['id'];?> .v').on('click', function() { actionView( '<?php echo siteUrl('language/view' . '/' . @$rec['id'] );?>', {}, 'idContentAreaDetect');} ) ;
					</script>
				</tr>
				<?php
			}
		}
		else
		{
			?>
				<tr><td colspan='9' style="text-align: center">No record found</td></tr>
			<?php
		}
	}
	?>
</table>
</form>

<?php echo $nav ?>
<?php echo $rpp ?>

<script type="text/javascript">
	submitForm( 'frmList<?php echo get_class($this);?>', null, function(){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable')}, null, true);
	bindCheckAll('.listchk-all', '.listchk-language:checkbox') ;
	bindActionButtons('hide') ;
	bindHightlightRow('<?php echo get_class($this);?>') ;
</script>