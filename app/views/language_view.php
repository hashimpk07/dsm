<div class="dvPageHeadingArea">
	<span class="heading">Language Details</span>
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
			<td>Group Name</td>
				<td>:</td>
				<td><?php echo $result['name']; ?></td>
		</tr>
		<tr class="<?php echo cls($result['font']);?>">
			<td>Font Name</td>
				<td>:</td>
				<td><?php echo $result['font']; ?></td>
		</tr>
		<tr class="<?php echo cls($result['dir']);?>">
			<td>Direction</td>
				<td>:</td>
				<td><?php echo (($result['dir'] == 'r') ? 'Right To Left' : 'Left To Right') ; ?></td>
		</tr>

	</table>

</div>