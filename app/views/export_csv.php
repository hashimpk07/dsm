<form style="position: absolute; right: 15px; top: 60px;" class="frm-export" target="_blank" action="<?php echo siteUrl(strtolower(get_class($this)));?>/search" method="get" >
    <button class="searchbtn searchbtncls exportcsv" type="submit" name="btnSearchCsvExport" style="float: right;" >
        <span class="flaticon-search" ></span>
    </button>
    <?php
		foreach( $this->fields as $k => $v )
		{
//			if( strtolower($k) != 'searchq' )
			{
			?>
				<input type="hidden" name="<?php echo $k;?>" value="<?php echo $v;?>" />
			<?php
			}
		}
	?>
</form>