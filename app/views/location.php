<div class="dvPageHeadingArea">
	<span class="heading">Locations</span>
	<span class="subheading">
		<?php
		$searchTarget = 'idListArea' . get_class($this);
		include 'search.php' ;
		if($this->accessAllowed('location', 'add') )
		{
			addButton( siteUrl('location/add'), NULL, 'idContentAreaBig') ;
		}?>
	</span>
</div>

<div id='idListArea<?php echo get_class($this);?>'>
	<?php echo $listtable; ?>
</div>