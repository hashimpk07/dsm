<div id="idCache"></div>
<input type="hidden" id="idCacheScreenId" value="<?php echo $screen_id;?>" />
<input type="hidden" id="idCacheBranchId" value="<?php echo $branch_id;?>" />

<script type="text/javascript" >
	setInterval(function(){
		getData( '<?php echo siteUrl('display/query_cache/' );?>', {'branchId' : jQuery('#idCacheBranchId').val() }, null, null, function(data) {
			
			
		} ) ;
	}, <?php echo intval(1 * 1000);?>) ;
</script>