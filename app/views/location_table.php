<?php //include('widget_excel.php');?>
<form action="<?php echo siteUrl('location/bulkaction');?>" method="post" id="frmList<?php echo get_class($this);?>" >
	<input type="hidden" id="hidbulkaction" name="hidbulkaction" value="" />
	<div class="list-iconset">
		<?php if($this->accessAllowed('location', 'delete') ) { ?>
		<span class="flaticon-trash def-flaticon" title="Delete" onclick="return onBulkActionClick('frmList<?php echo get_class($this);?>', 'delete', 'Delete selected location ?')" type="image" name="aclaction-delete" value="Delete Selected" id="idbulkdelete" />		
		<?php } ?>
	</div>

<table width="100%" style="float: left;" class="tab-grey">
	<tr>
		<th width="1"><input type="checkbox" class="listchk-all" /></th>
		<th width="1"><?php echo drawTh('#', '');?></th>
		<th class="m s" ><?php echo drawTh('Name', 'name');?></th>
	    
		
		<th class="m action-column" >Actions</th>
	</tr>



	<?php
	$i = 0 ;
	$groupOn = true ;
	if( isset($this->fields['searchq-col']) )
	{
		$groupOn = false ;
		if( $this->fields['searchq-col'] == 'name' )
		{
			$groupOn = true ;
		}
	}
	else
	{
		$groupOn = true ;
	}
	if( is_array($records) )
	{
		if( count($records) > 0 )
		{
			foreach ($records as $rec)
			{
				$bg = ($i++ % 2) ? 'bg' : 'nobg' ;
				?>
	<script>
	var ar=<?php echo json_encode($records) ?>;
	</script>
			<tr class="<?php echo $bg; ?>" id="idTableRow<?php echo $rec['id'];?>">
					<td><input name="cbList[]" value="<?php echo @$rec['id']; ?>" type="checkbox" class="listchk-item" /></td>
					<td class="v"><?php echo @$rec['qslno']; ?></td>
					<td class="m s v" onclick="actionView( '<?php echo siteUrl('location/view' . '/' . @$rec['id'] );?>', {}, 'idContentAreaDetect');"  ><?php echo @$rec['name'] ; ?></td>
					<td class="m action-column">
						<?php 
                        if($this->accessAllowed('location', 'edit') ) { ?>
						<span class="flaticon-edit" title="<?php echo l('Edit details');?>" onclick="actionEdit( '<?php echo siteUrl('location/edit' . '/' . @$rec['id'] );?>', {}, 'idContentAreaDetect');" />
						<?php  } ?>
						<?php if($this->accessAllowed('location', 'delete') ) { ?>
						<span class="flaticon flaticon-trash" title="<?php echo l('Delete');?>" onclick="actionFlag( '<?php echo siteUrl('location/delete' . '/' . @$rec['id'] );?>', {}, 'Are you sure you want to delete this location?', '', null, function(){htmlRefreshTable('idListArea<?php echo get_class($this);?>RefreshTable')}, true);" />
						<?php } ?>
						
					</td>
				</tr>
				<?php
			}
		}
		else
		{
			?>
				<tr><td colspan='8'  style="text-align: center">No record found</td></tr>
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