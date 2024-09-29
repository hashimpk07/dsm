<div class="dvPageHeadingArea">
	<span class="heading">Item Details</span>
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
			<td >Name</td>
				<td>:</td>
				<td><?php echo $result['name']; ?></td>
		</tr>
		<tr class="<?php echo cls($result['name']);?>">
			<td>Category</td>
				<td>:</td>
				<td><?php echo $result['categoryname']; ?></td>
		</tr>
		
		<?php 
			$consume='';
			if($result['type']=='C')
			{
				$consume='Consumable';
			}
            else if($result['type']=='A')
			{
				$consume='Asset';
			}?>
		<tr class="<?php echo cls($consume);?>">
			<td>Item Type</td>
			<td>:</td>
			<td>
			<?php echo $consume ?></td>
		</tr>
		
		
	</table>

</div>