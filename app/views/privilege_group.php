<div class="dvPageHeadingArea">
	<span class="heading">Privilege Groups</span>
	<span class="subheading">
		<?php
		$searchTarget = 'idListArea' . get_class($this);
		include 'search.php' ;
		if($this->accessAllowed('privilege_group', 'add') )
		{
			addButton( siteUrl('privilege_group/add'), NULL, 'idContentAreaBig') ;
		}?>
	</span>
</div>

<div id='idListArea<?php echo get_class($this);?>'>
	<?php echo $listtable; ?>
</div>