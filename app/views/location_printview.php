<div class="dvPageHeadingArea">
	<span class="heading">Locations Details</span>
	<div class="subheading">
		<?php if($this->accessAllowed('location', 'page') ) { ?>
			<?php } ?>
		
        
	</div>
</div>

<div class="viewgrpwrap">
	<table class="tab-transparent" width="100%">

		<tr class="<?php echo cls($result['name']);?>">
			<td >Name</td>
				<td>:</td>
                <td><?php echo $result['name']; ?></td>
		</tr>

		
	</table>
</div>

<script type="text/javascript">
	 window.print();
      window.close();
</script>
