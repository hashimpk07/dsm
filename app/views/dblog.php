<div class="dvPageHeadingArea">
	<span class="heading">Database Log</span>
	<span class="subheading">
		<?php
		$searchTarget = 'idListArea' . get_class($this);
		include 'search.php' ;
		?>
	</span>
</div>

<div id='idListArea<?php echo get_class($this);?>'>
	<?php echo $listtable; ?>
</div>