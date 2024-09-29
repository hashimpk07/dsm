<div class="dvPageHeadingArea">
	<span class="heading">Languages</span>
	<span class="subheading">
		<?php
		$searchTarget = 'idListArea' . get_class($this);
		include 'search.php' ;
		if($this->accessAllowed('language', 'add') )
		{
			addButton( siteUrl('language/add'), NULL, 'idContentAreaBig') ;
		}?>
	</span>
</div>

<div id='idListArea<?php echo get_class($this);?>'>
	<?php echo $listtable; ?>
</div>