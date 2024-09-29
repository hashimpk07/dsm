<div class="dvPageHeadingArea">
	<span class="heading">Branches</span>
	<span class="subheading">
		<?php
		$searchTarget = 'idListArea' . get_class($this);
		include 'search.php' ;
		if($this->accessAllowed('branch', 'add') )
		{
			if( $this->session->get('usr_grp_code') != QC_USR_BRANCH_OP ) {
				addButton( siteUrl('branch/add'), NULL, 'idContentAreaBig') ;
			}
		}?>
	</span>
</div>

<div id='idListArea<?php echo get_class($this);?>'>
	<?php echo $listtable; ?>
</div>