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
			$cusStr = " AND ag_cus_id='$uid' " ;
		}
		$sqlexp = "SELECT COUNT(*) as total FROM agreement WHERE ag_meeting = 0 AND DATE(ag_end_dt) < CURDATE() $cusStr " ;
		$expcount = $QFC->db->scalarField($sqlexp) ;

		$sqltoday = "SELECT COUNT(*) as total FROM agreement WHERE ag_meeting = 0 AND ag_terminated=0 AND DATE(ag_end_dt) = CURDATE() $cusStr " ;
		$todaycount = $QFC->db->scalarField($sqltoday) ;
		
		$sql30 = "SELECT COUNT(*) as total FROM agreement WHERE ag_meeting = 0 AND ag_terminated=0 AND ( DATE(ag_end_dt) <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND DATE(ag_end_dt) > CURDATE()) $cusStr " ;
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
				<div class="click" onclick="actionView('<?php echo siteUrl('agreement/page'); ?>', {}, 'idContentAreaSmall')" >Agreement</div>
			</caption>
			<tr>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('agreement/page?arg-gen-type=e'); ?>', {}, 'idContentAreaSmall')" >
						<b>E</b><i>xpired :</i> <u><?php echo intval($expcount); ?></u>
					</div>
				</td>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('agreement/page?arg-gen-type=t'); ?>', {}, 'idContentAreaSmall')" >
						<b>T</b><i>oday :</i> <u><?php echo intval($todaycount); ?></u>
					</div>
				</td>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('agreement/page?arg-gen-type=m'); ?>', {}, 'idContentAreaSmall')" >
						<b>I</b><i>n 30 Days:</i> <u><?php echo intval($in3count); ?></u>
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