<?php include('widget_excel.php');?>
<form action="<?php echo siteUrl('dblog/bulkaction');?>" method="post" id="frmList<?php echo get_class($this);?>" >
	<input type="hidden" id="hidbulkaction" name="hidbulkaction" value="" />
	

<table width="100%" style="float: left;" class="tab-grey">
	<tr>
	
		<th width="1"><?php echo drawTh('#', 'log_id');?></th>
		<th class="m s" ><?php echo drawTh('Employee ', 'emp_name');?></th>
                <th class="s" ><?php echo drawTh('Action', 'log_action');?></th>
                <th class="s" ><?php echo drawTh('Data', 'log_data');?></th>
                <th class="s" ><?php echo drawTh('Date', 'log_dt');?></th>
		
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
                                 $temp='';
         if( $rec['log_action'] == 'I' ) 
                {
                $temp='Add';
                }
                 if( $rec['log_action'] == 'D' ) 
                {
                $temp='Delete';
                }
                 if( $rec['log_action'] == 'U' ) 
                {
                $temp='Update';
                }
                 if( $rec['log_action'] == 'T' ) 
                {
                $temp='Truncate';
                }
				?>
				<tr class="<?php echo $bg; ?>" id="idTableRow<?php echo $rec['log_id'];?>">
					
					<td class="v" onclick="actionView( '<?php echo siteUrl('dblog/view' . '/' . @$rec['log_id'] );?>', {}, 'idContentAreaDetect');" ><?php echo @$rec['qslno']; ?></td>
					<td class="m s v" onclick="actionView( '<?php echo siteUrl('dblog/view' . '/' . @$rec['log_id'] );?>', {}, 'idContentAreaDetect');" ><?php echo @$rec['emp_name']; ?></td>
                                        <td class="v" onclick="actionView( '<?php echo siteUrl('dblog/view' . '/' . @$rec['log_id'] );?>', {}, 'idContentAreaDetect');" ><?php echo @$temp; ?></td>
                                        <td class="v" onclick="actionView( '<?php echo siteUrl('dblog/view' . '/' . @$rec['log_id'] );?>', {}, 'idContentAreaDetect');" ><?php echo @$rec['log_data']; ?></td>
                                        <td class="v" onclick="actionView( '<?php echo siteUrl('dblog/view' . '/' . @$rec['log_id'] );?>', {}, 'idContentAreaDetect');" ><?php echo clientDateTime(@$rec['log_dt']); ?></td>
						
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