<?php

	//Type 1
	$searchdt = date('d-m-Y') ; 
	
	$retTotal			= 0 ; //Sum of $new messages.

	$UGC = $QFC->session->get('usr_grp_code') ;
	if( $QFC->session->get('usr_id') && $UGC  )
	{
		$sql = "SELECT req_type, COUNT(*) as total FROM request WHERE req_msg_read='0' GROUP BY req_type" ;
		$reqset = $QFC->db->fetchKV($sql, 'req_type', 'total') ;				
	}
?>

<li class='notification-node <?php echo $class ; ?>' >
	<div class="<?php echo $flaticon;?>" ></div>
	<div class="data" >
		<table>
			<caption>
				<div class="<?php echo $flaticon;?>" ></div>
				<div>Requests</div>
			</caption>
			<tr>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('requestadmin/page/facilityrequest'); ?>', {}, 'idContentAreaSmall')" >
						<b>F</b><i>acilities :</i> <u><?php echo ($facilities_tot = intval(@$reqset[QC_REQ_FACILITY])); ?></u>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('requestadmin/page/servicerequest'); ?>', {}, 'idContentAreaSmall')" >
						<b>S</b><i>ervices :</i> <u><?php echo ($services_tot = intval(@$reqset[QC_REQ_SERVICE])); ?></u>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('requestadmin/page/maintenancerequest'); ?>', {}, 'idContentAreaSmall')" >
						<b>M</b><i>aintenance :</i> <u><?php echo ($maitenance_tot = intval(@$reqset[QC_REQ_MAINTENANCE])); ?></u>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('requestadmin/page/complaints'); ?>', {}, 'idContentAreaSmall')" >
						<b>C</b><i>omplaints :</i> <u><?php echo ($complaint_tot = intval(@$reqset[QC_REQ_COMPLAINT])); ?></u>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('requestadmin/page/feedbacks'); ?>', {}, 'idContentAreaSmall')" >
						<b>F</b><i>eedback :</i> <u><?php echo ($feedback_tot = intval(@$reqset[QC_REQ_FEEDBACK])); ?></u>
					</div>
				</td>
			</tr>
		</table>
	</div>
</li>

<?php
	$retTotal = $facilities_tot + $services_tot + $maitenance_tot + $complaint_tot + $feedback_tot ;
	return intval($retTotal);
?>