<div class="dvPageHeadingArea">
	<span class="heading">Database Log Details</span>
	<div class="subheading">
		<?php if($this->accessAllowed('dblog', 'page') ) { ?>
		<span class="flaticon flaticon-list" onclick="onListIconClick('idListArea<?php echo get_class($this);?>', '<?php echo siteUrl('dblog/page'); ?>', {}, 'idContentAreaDetect')" ></span>
		<?php } ?>
		<span class="flaticon flaticon-plus" onclick="onHideitIconClick(this);"></span>
	</div>
</div>

<div class="viewgrpwrap">

	<table class="tab-transparent" width="100%">
		
                <tr class="<?php echo cls($result['emp_name']);?>">
			<td>Employee Name</td>
				<td>:</td>
				<td><?php echo $result['emp_name']; ?></td>
		</tr>
             <?php
             $temp='';
           if( $result['log_action'] == 'I' ) 
                {
                $temp='Add';
                }
                 if( $result['log_action'] == 'D' ) 
                {
                $temp='Delete';
                }
                 if( $result['log_action'] == 'U' ) 
                {
                $temp='Update';
                }
                 if( $result['log_action'] == 'T' ) 
                {
                $temp='Truncate';
                }
                ?>
                
		<tr class="<?php echo cls($temp);?>" >
			<td>Action</td>
				<td>:</td>
				<td><?php echo $temp;?></td>
		</tr>
		<tr class="<?php echo cls($result['log_data']);?>">
			<td>Data</td>
				<td>:</td>
				<td><?php echo $result['log_data']; ?></td>
		</tr>
		<tr class="<?php echo cls($result['log_dt']);?>">
			<td>Date</td>
				<td>:</td>
				<td><?php echo $result['log_dt']; ?></td>
		</tr>
		
		
		
	</table>

</div>