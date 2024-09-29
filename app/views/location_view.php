<div class="dvPageHeadingArea">
	<span class="heading">Location Details</span>
	<div class="subheading">
		<?php if($this->accessAllowed('location', 'page') ) { ?>
		<span class="flaticon flaticon-list" onclick="onListIconClick('idListArea<?php echo get_class($this);?>', '<?php echo siteUrl('location/page'); ?>', {}, 'idContentAreaDetect')" ></span>
		<?php } ?>
		<span class="flaticon flaticon-plus" onclick="onHideitIconClick(this);"></span>
	</div>
</div>

<div class="viewgrpwrap">

	<table class="tab-transparent" width="100%">
        
		<tr class="<?php echo cls($result['name']);?>">
			<td >Name</td>
				<td>:</td>
                <td><?php echo $result['name']; ?></td>
		</tr>
		
	
		
		
		
	</table>
</div>
