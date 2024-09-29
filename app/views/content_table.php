<form action="<?php echo siteUrl('content/bulkaction');?>" method="post" id="frmList<?php echo get_class($this);?>" >
	<input type="hidden" id="hidbulkaction" name="hidbulkaction" value="" />
	<div class="list-iconset">
		<?php if($this->accessAllowed('content', 'delete') ) { ?>
		<span class="flaticon-trash def-flaticon" title="Delete" onclick="return onBulkActionClick('frmList<?php echo get_class($this);?>', 'delete', 'Delete selected contents ?')" type="image" name="aclaction-delete" value="Delete Selected" id="idbulkdelete" />		
		<?php } ?>
	</div>

<table width="100%" style="float: left;" class="tab-grey">
	<tr>
		<th width="1"><input type="checkbox" class="listchk-all" /></th>
		<th width="1"><?php echo drawTh('#', 'id');?></th>
		<th class="m s" ><?php echo drawTh('Name', 'title');?></th>
		<th class="s" ><?php echo drawTh('Content Type', 'type');?></th>
		<th class="s" ><?php echo drawTh('Group', 'classification');?></th>
		<th class="s" ><?php echo drawTh('Date', 'dt');?></th>
		<th class="s" ><?php echo drawTh('Approval By', 'u.username');?></th>
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
				$groupName = '' ;
				switch( $rec['classification'] )
				{
					case 'B':
						$groupName = 'Branch Message' ;
						break;
					case 'G':
						$groupName = 'Global Message' ;
						break;
					default:
						$groupName = 'General' ;
						break;
				}
				$bg = ($i++ % 2) ? 'bg' : 'nobg' ;
				?>
				<tr class="<?php echo $bg; ?>" id="idTableRow<?php echo $rec['id'];?>">
					<td><input name="cbList[]" value="<?php echo @$rec['id']; ?>" type="checkbox" class="listchk-content" /></td>
					<td class="v"><?php echo @$rec['qslno']; ?></td>
					<td class="m s"><?php
					echo @$rec['title'] ;
					?></td>
					<td class="v"><?php echo $this->getModel('content_type_model')->explain(@$rec['type']); ?></td>
					<td class="v"><?php echo $groupName ; ?></td>
					<td class="v"><?php echo clientDateTime( @$rec['dt'] ) ; ?></td>
					<td class="v"><?php echo @$rec['username'] ; ?></td>
					<td class="m action-column">
						<?php if($this->accessAllowed('content', 'edit') ) { ?>
						<span class="flaticon-edit" title="<?php echo l('Edit details');?>" onclick="actionEdit( '<?php echo siteUrl('content/edit' . '/' . @$rec['id'] );?>', {}, 'idContentAreaDetect');" />
						<?php } ?>
						<?php if($this->accessAllowed('telecast', 'add') ) { ?>
						<span class="flaticon-plus" title="<?php echo l('Telecast Schedule');?>" onclick="actionEdit( '<?php echo siteUrl('telecast/add/' . intval(@$rec['id']) . '/' . intval(@$rec['branch_id']) );?>', {}, 'idContentAreaDetect');" />
						<?php } ?>
						<?php 
						if($this->accessAllowed('content', 'approve') ) 
						{
							if( $rec['approved'] )
							{
							?>
						<span class="flaticon-block" title="Reject" onclick="actionFlag( '<?php echo siteUrl('content/approve' . '/' . @$rec['id'] );?>/0', {}, null, 'idWorkArea', null, function(){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable')}, true);" />
						<?php
							}
							else
							{
							?>
						<span class="flaticon-unblock" title="Approve" onclick="actionFlag( '<?php echo siteUrl('content/approve' . '/' . @$rec['id'] );?>/1', {}, null, 'idWorkArea', null, function(){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable')}, true);" />
						<?php	
							}
						}
						?>

						<?php 
						if( ! $rec['future'] )
						{
							if($this->accessAllowed('content', 'delete') ) { ?>
						<span class="flaticon flaticon-trash" title="<?php echo l('Delete');?>" onclick="actionFlag( '<?php echo siteUrl('content/delete' . '/' . @$rec['id'] );?>', {}, 'Are you sure you want to delete this content?', '', null, function(){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable')}, true);" />
						<?php
							}
						} ?>

					</td>
					<script type="text/javascript">
						jQuery('#idTableRow<?php echo $rec['id'];?> .v').on('click', function() { actionView( '<?php echo siteUrl('content/view' . '/' . @$rec['id'] );?>', {}, 'idContentAreaDetect');} ) ;
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
	bindCheckAll('.listchk-all', '.listchk-content:checkbox') ;
	bindActionButtons('hide') ;
	bindHightlightRow('<?php echo get_class($this);?>') ;
</script>