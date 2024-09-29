<?php include('widget_excel.php');?>
<form action="<?php echo siteUrl('parameters/bulkaction');?>" method="post" id="frmList<?php echo get_class($this);?>" >
	<input type="hidden" id="hidbulkaction" name="hidbulkaction" value="" />
	<div class="list-iconset">
		<?php if($this->accessAllowed('parameters', 'delete') ) { ?>
		<span class="flaticon-trash def-flaticon" title="Delete" onclick="return onBulkActionClick('frmList<?php echo get_class($this);?>', 'delete', 'Delete selected Parameters ?')" type="image" name="aclaction-delete" value="Delete Selected" id="idbulkdelete" />		
		<?php } ?>
	</div>

<table width="100%" style="float: left;" class="tab-grey">
	<tr>
		<th width="1"><input type="checkbox" class="listchk-all" /></th>
		<th width="1"><?php echo drawTh('#', 'pm_id');?></th>
		<th class="m s" ><?php echo drawTh('Name', 'pm_title');?></th>
		
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
				<tr class="<?php echo $bg; ?>" id="idTableRow<?php echo $rec['pm_id'];?>">
					<td><input name="cbList[]" value="<?php echo @$rec['pm_id']; ?>" type="checkbox" class="listchk-item" /></td>
					<td class="v" onclick="actionView( '<?php echo siteUrl('parameters/view' . '/' . @$rec['pm_id'] );?>', {}, 'idContentAreaDetect');" ><?php echo @$rec['qslno']; ?></td>
					<td class="m s v" onclick="actionView( '<?php echo siteUrl('parameters/view' . '/' . @$rec['pm_id'] );?>', {}, 'idContentAreaDetect');" ><?php echo @$rec['pm_title']; ?></td>
					
					
					<td class="m action-column">
						<?php if($rec['pm_editable']==1)
                        {
                        if($this->accessAllowed('parameters', 'edit') ) { ?>
						<span class="flaticon-edit" title="<?php echo l('Edit details');?>" onclick="actionEdit( '<?php echo siteUrl('parameters/edit' . '/' . @$rec['pm_id'] );?>', {}, 'idContentAreaDetect');" />
						<?php }  } ?>
						<?php if($this->accessAllowed('parameters', 'delete') ) { ?>
						<span class="flaticon flaticon-trash" title="<?php echo l('Delete');?>" onclick="actionFlag( '<?php echo siteUrl('parameters/delete' . '/' . @$rec['pm_id'] );?>', {}, 'Are you sure you want to delete this Parameter?', '', null, function(){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable')}, true);" />
						<?php } ?>
					</td>
				</tr>
				<?php
			}
		}
		else
		{
			?>
			<tr><td colspan='8' style="text-align:center">No record found</td></tr>
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
	bindCheckAll('.listchk-all', '.listchk-item:checkbox') ;
	bindActionButtons('hide') ;
	bindHightlightRow('<?php echo get_class($this);?>') ;
</script>