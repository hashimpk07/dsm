<form action="<?php echo siteUrl('branch/bulkaction');?>" method="post" id="frmList<?php echo get_class($this);?>" >
	<input type="hidden" id="hidbulkaction" name="hidbulkaction" value="" />
	<input type="hidden" id="hidbulkprompt" name="hidbulkprompt" value="" />
	<div class="list-iconset">
		<?php if($this->accessAllowed('branch', 'delete') ) { ?>
		<span class="flaticon-trash def-flaticon" title="Delete" onclick="return onBulkActionClick('frmList<?php echo get_class($this);?>', 'delete', 'Delete selected branchs ?')" type="image" name="aclaction-delete" value="Delete Selected" id="idbulkdelete" />
		<?php } ?>
		<?php if($this->accessAllowed('branch', 'group') ) { ?>
		<span class="flaticon-masters def-flaticon" title="Create Group From Selected" onclick="return onBulkActionClick('frmList<?php echo get_class($this);?>', 'group', 'Enter group name ', true)" type="image" name="aclaction-group" value="Group Selected" id="idbulkgroup" />
		<?php } ?>
	</div>

<table width="100%" style="float: left;" class="tab-grey">
	<tr>
		<th width="1"><input type="checkbox" class="listchk-all" /></th>
		<th width="1"><?php echo drawTh('#', 'id');?></th>
		<th class="m s" ><?php echo drawTh('Name', 'b.name');?></th>
		<th class="m" ><?php echo drawTh('Code', 'b.code');?></th>
		<th><?php echo drawTh('Active Screen', 's.name');?></th>
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
					<td><input name="cbList[]" value="<?php echo @$rec['id']; ?>" type="checkbox" class="listchk-branch" /></td>
					<td class="v"><?php echo @$rec['qslno']; ?></td>
					<td class="m s v"><?php echo @$rec['name']; ?></td>
					<td class="m"><?php echo @$rec['code']; ?></td>
					<td class="v"><?php echo @$rec['screen']; ?></td>
					<td class="m action-column">
						<a target="_blank" href="<?php echo siteUrl('display/d' . '/' . @$rec['code'] );?>">
							<span class="flaticon-communication" title="<?php echo l('Preview');?>"  />
						</a>
						<?php if($this->accessAllowed('branch', 'edit') ) { ?>
						<span class="flaticon-edit" title="<?php echo l('Edit details');?>" onclick="actionEdit( '<?php echo siteUrl('branch/edit' . '/' . @$rec['id'] );?>', {}, 'idContentAreaDetect');" />
						<?php } ?>
						<?php if($this->accessAllowed('branch', 'delete') ) { ?>
						<span class="flaticon flaticon-trash" title="<?php echo l('Delete');?>" onclick="actionFlag( '<?php echo siteUrl('branch/delete' . '/' . @$rec['id'] );?>', {}, 'Are you sure you want to delete this branch?', '', null, function(){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable')}, true);" />
						<?php } ?>
					</td>
					<script type="text/javascript">
						jQuery('#idTableRow<?php echo $rec['id'];?> .v').on('click', function() { actionView( '<?php echo siteUrl('branch/view' . '/' . @$rec['id'] );?>', {}, 'idContentAreaDetect');} ) ;
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
	bindCheckAll('.listchk-all', '.listchk-branch:checkbox') ;
	bindActionButtons('hide') ;
	bindHightlightRow('<?php echo get_class($this);?>') ;
</script>