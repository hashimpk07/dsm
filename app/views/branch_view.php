<div class="dvPageHeadingArea">
	<span class="heading">Branch Details</span>
	<div class="subheading">
		<?php if($this->accessAllowed('item', 'page') ) { ?>
		<span class="flaticon flaticon-list" onclick="onListIconClick('idListArea<?php echo get_class($this);?>', '<?php echo siteUrl('item/page'); ?>', {}, 'idContentAreaDetect')" ></span>
		<?php } ?>
		<span class="flaticon flaticon-plus" onclick="onHideitIconClick(this);"></span>
	</div>
</div>

<div class="viewgrpwrap">

	<table class="tab-transparent" width="100%">
		<tr class="<?php echo cls($result['name']);?>">
			<td>Branch Name</td>
				<td>:</td>
				<td><?php echo $result['name']; ?></td>
		</tr>
		<tr class="<?php echo cls($result['code']);?>">
			<td>Branch Code</td>
				<td>:</td>
				<td><?php echo $result['code']; ?></td>
		</tr>
		<tr class="<?php echo cls($result['screen']);?>">
			<td>Screen</td>
				<td>:</td>
				<td><?php echo $result['screen']; ?></td>
		</tr>
		<tr class="<?php echo cls($result['country']);?>">
			<td>Country</td>
				<td>:</td>
				<td><?php echo $result['country']; ?></td>
		</tr>
		<tr class="<?php echo cls($result['state']);?>">
			<td>State</td>
				<td>:</td>
				<td><?php echo $result['state']; ?></td>
		</tr>
		<tr class="<?php echo cls($result['city']);?>">
			<td>City</td>
				<td>:</td>
				<td><?php echo $result['city']; ?></td>
		</tr>
		
		
		
	</table>

</div>