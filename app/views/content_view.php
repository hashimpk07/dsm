<div class="dvPageHeadingArea">
	<span class="heading">Content Details</span>
	<div class="subheading">
		<?php if($this->accessAllowed('content', 'page') ) { ?>
		<span class="flaticon flaticon-list" onclick="onListIconClick('idListArea<?php echo get_class($this);?>', '<?php echo siteUrl('category/page'); ?>', {}, 'idContentAreaDetect')" ></span>
		<?php } ?>
		<span class="flaticon flaticon-plus" onclick="onHideitIconClick(this);"></span>
	</div>
</div>

<div class="viewgrpwrap">

	<table class="tab-transparent" width="100%">
        
		<tr class="<?php echo cls($result['title']);?>">
			<td >Title</td>
				<td>:</td>
                <td><?php echo $result['title']?></td>
		</tr>
		<tr class="<?php echo cls($result['type']);?>">
			<td >Type</td>
				<td>:</td>
                <td><?php echo $this->loadModel('content_type_model')->explain($result['type']) ; ?></td>
		</tr>
		
		<?php 
		foreach( $result['lang_data'] as $v )
		{
		?>
		<tr class="<?php echo cls(@$v['data'])?>">
			<td >Data <?php echo ' - ' . $v['language'];?></td>
				<td>:</td>
				<td>
					<?php
						switch($result['type'])
						{
							case QC_CONTENT_TYPE_I :
								echo '<img src="' . baseUrl('app/data/' . $v['data']) . '" width="200" style="max-height: 200px" >' ;
								break ;
							case QC_CONTENT_TYPE_V:
								echo '<video src="' . baseUrl('app/data/' . $v['data']) . '" width="200" style="max-height: 200px" >' ;
								break ;
							case QC_CONTENT_TYPE_IU :
								echo '<img src="' .  $v['data'] . '" width="200" style="max-height: 200px" />' ;
								break ;
							case QC_CONTENT_TYPE_VU:
								echo '<video src="' . $v['data'] . '" width="200" style="max-height: 200px" />' ;
								break ;
							case QC_CONTENT_TYPE_T :
								$dir = ( ($v['dir'] == 'l') ? 'direction="ltr"' : 'directio="rtl"' ) ;
								echo '<span ' . $dir . ' >' . $v['data'] . '</span>' ;
								break ;
							case QC_CONTENT_TYPE_S :
								$dir = ( ($v['dir'] == 'l') ? 'direction="left"' : 'direction="right"' ) ;
								echo '<marquee ' . $dir . ' >' . $v['data'] . '</marquee>' ;
								break ;
							case QC_CONTENT_TYPE_H :
								echo '<span>' . $v['data'] . '</span>' ;
								break ;
						}
					?>
				</td>
		</tr>
		<?php
		}
		?>
	</table>
</div>