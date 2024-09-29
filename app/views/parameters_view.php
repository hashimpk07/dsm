<div class="dvPageHeadingArea">
	<span class="heading">Parameters Details</span>
	<div class="subheading">
		<?php if($this->accessAllowed('parameters', 'page') ) { ?>
		<span class="flaticon flaticon-list" onclick="onListIconClick('idListArea<?php echo get_class($this);?>', '<?php echo siteUrl('parameters/page'); ?>', {}, 'idContentAreaDetect')" ></span>
		<?php } ?>
		<span class="flaticon flaticon-plus" onclick="onHideitIconClick(this);"></span>
	</div>
</div>

<div class="viewgrpwrap">

	<table class="tab-transparent" width="100%">
		<tr class="<?php echo cls($result['pm_title']);?>">
			<td >Title</td>
				<td>:</td>
				<td><?php echo $result['pm_title']; ?></td>
		</tr>
		<tr class="<?php echo cls($result['pm_code']);?>" >
			<td>Code</td>
				<td>:</td>
				<td><?php echo $result['pm_code'];?></td>
		</tr>
		<tr class="<?php echo cls($result['pm_value']);?>">
			<td>Value</td>
				<td>:</td>
				<td><?php echo $result['pm_value']; ?></td>
		</tr>
		
		
		
		
	</table>

</div>