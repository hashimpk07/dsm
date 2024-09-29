<div class="dvPageHeadingArea">
	<span class="heading">Items</span>
	<span class="subheading">
		<?php
		$searchTarget = 'idListArea' . get_class($this);
		include 'search.php' ;
		if($this->accessAllowed('item', 'add') )
		{
			addButton( siteUrl('item/add'), NULL, 'idContentAreaBig') ;
		}?>
	</span>
</div>

<div id='idListArea<?php echo get_class($this);?>'>
	<?php echo $listtable; ?>
</div>