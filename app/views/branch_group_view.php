<div class="dvPageHeadingArea">
	<span class="heading">Branch Group Details</span>
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
			<td>Branch Group Name</td>
				<td>:</td>
				<td><?php echo $result['name']; ?></td>
		</tr>
		<tr class="<?php echo cls(count($entries));?>">
			<td>Branches</td>
				<td>:</td>
				<td>
				<?php
				foreach( $entries as $one ) { 
				?>
					<span><?php echo $one['name'];?></span><br/>
				<?php } ?>
				</td>
		</tr>
		
		
		
	</table>

</div>