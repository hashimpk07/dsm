<div class="dvPageHeadingArea">
	<span class="heading">Parameters</span>
	<span class="subheading">
		<?php
		$searchTarget = 'idListArea' . get_class($this);
		include 'search.php' ;
		if($this->accessAllowed('parameters', 'add') )
		{
			addButton( siteUrl('parameters/add'), NULL, 'idContentAreaBig') ;
		}?>
	</span>
</div>

<div id='idListArea<?php echo get_class($this);?>'>
	<?php echo $listtable; ?>
</div>