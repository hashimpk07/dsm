<!--<div class="dvPageHeadingArea">
	<span class="heading">Settings</span>
	<span class="subheading">
		<?php
		//$searchTarget = 'idListArea' . get_class($this);
		//include 'search.php' ;
		//if($this->accessAllowed('parameter', 'add') )
		//{
		//	addButton( siteUrl('parameter/add'), NULL, 'idContentAreaBig') ;
		//}?>
	</span>
</div>-->

<div id='idListArea<?php echo get_class($this);?>'>
	<?php echo $listtable; ?>
</div>