<?php
	//Type 1
	$searchdt = date('d-m-Y') ; 

	$retTotal			= 0 ; //Sum of $new messages.

	global $QFC ;
	$UGC = $QFC->session->get('usr_grp_code') ;
	$uid = $QFC->session->get('usr_id') ;
	
	$expcount = 0 ;
	$todaycount = 0 ;
	$in3count = 0 ;

	if( $uid )
	{
		$cusStr = '' ;
		if( $UGC == QC_USR_CUSTOMER )
		{
			$cusStr = " AND bl_cus_id='$uid' " ;
		}
		$sqlexp = "SELECT COUNT(*) as total FROM bill bl"
			. " INNER JOIN customers c ON c.cus_id=bl.bl_cus_id "
			. " INNER JOIN agreement ag ON ag.ag_id=bl.bl_ag_id WHERE DATE(bl_due_dt) < CURDATE() $cusStr " ;
		$expcount = $QFC->db->scalarField($sqlexp) ;

		$sqltoday = "SELECT COUNT(*) as total FROM bill bl"
			. " INNER JOIN customers c ON c.cus_id=bl.bl_cus_id "
			. " INNER JOIN agreement ag ON ag.ag_id=bl.bl_ag_id WHERE DATE(bl_due_dt) = CURDATE() $cusStr " ;
		$todaycount = $QFC->db->scalarField($sqltoday) ;

		$sql30 = "SELECT COUNT(*) as total FROM bill bl "
			. " INNER JOIN customers c ON c.cus_id=bl.bl_cus_id "
			. " INNER JOIN agreement ag ON ag.ag_id=bl.bl_ag_id WHERE DATE(bl_due_dt) < DATE_ADD(CURDATE(), INTERVAL 3 DAY) $cusStr " ;
		$in3count = $QFC->db->scalarField($sql30) ;
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
				<div class="click" onclick="actionView('<?php echo siteUrl('bill_payment/page'); ?>', {}, 'idContentAreaSmall')" >Bill</div>
			</caption>
			<tr>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('bill_payment/page?arg-gen-type=o'); ?>', {}, 'idContentAreaSmall')" >
						<b>O</b><i>verdue :</i> <u><?php echo intval($expcount); ?></u>
					</div>
				</td>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('bill_payment/page?arg-gen-type=t'); ?>', {}, 'idContentAreaSmall')" >
						<b>T</b><i>oday :</i> <u><?php echo intval($todaycount); ?></u>
					</div>
				</td>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('bill_payment/page?arg-gen-type=d'); ?>', {}, 'idContentAreaSmall')" >
						<b>I</b><i>n 3 Days:</i> <u><?php echo intval($in3count); ?></u>
					</div>
				</td>
			</tr>
		</table>		
	</div>
</li>

<?php
	$retTotal = $expcount + $todaycount + $in3count ;
	return intval($retTotal);
?>