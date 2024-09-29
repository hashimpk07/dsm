<ul class="pop-notification">
<?php
	//Type 1
	include 'notification_header.php' ;
	$cntNotification = 0 ;

	global $QFC;
	
	//lead
	$c = $QFC->loadController('lead_followups') ;
	if( $c->accessAllowed('lead_followups', 'page', null) )
	{
		$cntNotification += include 'notification_lead_followups.php' ;
	}
	//agreement due
	$c = $QFC->loadController('agreement') ;
	if( $c->accessAllowed('agreement', 'page', null) )
	{
		$cntNotification += include 'notification_agreement.php' ;
	}

	//bill due
	$c = $QFC->loadController('bill_payment') ;
	if( $c->accessAllowed('bill_payment', 'page', null) )
	{
		$cntNotification += include 'notification_bill.php' ;
	}
	//request
	$c = $QFC->loadController('requestadmin') ;
	if( $c->accessAllowed('requestadmin', 'page', null) )
	{
		$cntNotification += include 'notification_requestadmin.php' ;
	}

	//request customer side
	$c = $QFC->loadController('request') ;
	if( !$c->accessAllowed('request', 'page', null) )
	{
		$cntNotification += include 'notification_request.php' ;
	}
	include 'notification_footer.php' ;
?>
</ul>

<script type="text/javascript">
	jQuery('.pop-notification span').click(function(){
		jQuery('.pop-notification').slideToggle('fast') ;
		return false ;
	}) ;

	jQuery('.pop-notification-count').html('<?php echo $cntNotification;?>') ;
</script>

<!-- Pull other alerts -->
<?php
if( @PRM_CRON_DOWN )
{
	$sql = "SELECT pm_html FROM parameters WHERE pm_code = 'PRM_CRON_DOWN' " ;
	$cronSaid = $this->db->scalarField($sql) ;
?>
<script type="text/javascript">
	setHtml('idFailureMsg', '<?php echo $cronSaid;?>', 100000 );
</script>
<?php
}
?>