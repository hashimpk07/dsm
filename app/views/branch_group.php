<div class="dvPageHeadingArea">
	<span class="heading">Branch Groups</span>
	<span class="subheading">
		<?php
		$searchTarget = 'idListArea' . get_class($this);
		include 'search.php' ;
		if($this->accessAllowed('branch_group', 'add') )
		{
			if( $this->session->get('usr_grp_code') != QC_USR_BRANCH_OP ) {
				addButton( siteUrl('branch_group/add'), NULL, 'idContentAreaBig') ;
			}
		}?>
	</span>
</div>

<div id='idListArea<?php echo get_class($this);?>'>
	<?php echo $listtable; ?>
</div>