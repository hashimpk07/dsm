<?php

	//Type 1
	$searchdt = date('d-m-Y') ; 

	$retTotal			= 0 ; //Sum of $new messages.

	$UGC = $QFC->session->get('usr_grp_code') ;
	$uid = $QFC->session->get('usr_id') ;
	if( $uid && $UGC == QC_USR_CUSTOMER )
	{
		$sql = "SELECT COUNT(*) as total FROM message WHERE msg_to_id='$uid' AND msg_to_grp_code='" . QC_USR_CUSTOMER . "' AND msg_read=0" ;
		$msgcount = $QFC->db->scalarField($sql) ;
	}
	$class = ' whitebg' ;
	$flaticon = 'flaticon flaticon-information' ;
?>

<li class='notification-node <?php echo $class ; ?>' >
	<div class="<?php echo $flaticon;?>" ></div>
	<div class="data" >
		<table>
			<caption>
				<div class="<?php echo $flaticon;?>" ></div>
				<div>Messages</div>
			</caption>
			<tr>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('request/page'); ?>', {}, 'idContentAreaSmall')" >
						<b>M</b><i>essages :</i> <u><?php echo intval($msgcount); ?></u>
					</div>
				</td>
			</tr>
		</table>		
	</div>
</li>

<?php
	$retTotal = $msgcount ;
	return intval($retTotal);
?>