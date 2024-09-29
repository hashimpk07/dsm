<div class="dvPageHeadingArea">
	<span class="heading">Contents</span>
	<span class="subheading">
		<?php
		$searchTarget = 'idListArea' . get_class($this);
		include 'search.php' ;
		if($this->accessAllowed('content', 'add') )
		{
			addButton( siteUrl('content/add'), NULL, 'idContentAreaBig') ;
		}
		if($this->accessAllowed('content', 'branch_message') )
		{
		?>
		<input type="button" value="Branch Message" onclick="actionView( 'content/branch_message', {}, 'idContentAreaBig');" style="margin: 0 3px 3px 0; float: right;" />
		<?php
		}
		if($this->accessAllowed('content', 'global_message') )
		{
		?>		
		<input type="button" value="Global Message" onclick="actionView( 'content/global_message', {}, 'idContentAreaBig');" style="margin: 0 3px 3px 0; float: right;" />
		<?php
		}
		?>
	</span>
</div>

<div id='idListArea<?php echo get_class($this);?>'>
	<?php echo $listtable; ?>
</div>