<?php
	//Type 1
	$searchdt = date('d-m-Y') ; 
	$total   = 0 ;
	$missed  = 0 ;
	$today   = 0 ;
	$pending = 0 ;

	if( $QFC->session->get('usr_id') )
	{
		$cond = '' ;
		if( ! isOtherData(1) )
		{
			$uid = $this->session->get('usr_id') ;
			if( $uid && $uid != QC_ADMIN_UID )
			{
				$cond = " AND l.lead_owner='$uid' " ;
			}
		}

		$qdt = mysqlDate($searchdt) ;
		$sqtmp = "SELECT COUNT(*) as cnt FROM lead_followups f
					INNER JOIN leads l ON l.lead_id=f.flw_lead_id 
					LEFT JOIN lead_com_types c ON c.ctyp_id=f.flw_ctyp_id 
					LEFT JOIN lead_status ls ON ls.ls_id=f.flw_status WHERE f.flw_status=2 AND flw_deleted=0 $cond
			AND 
			(DATE_FORMAT(f.flw_next_dt, '%Y-%m-%d') {REPLACE_STRING} '$qdt') AND f.flw_next_dt != '0000-00-00' AND f.flw_status!= 1 AND f.flw_status!=3 AND f.flw_id IN( SELECT MAX(flw_id) as flw_id FROM lead_followups WHERE flw_deleted=0 GROUP BY flw_lead_id )" ;

		$missedq = str_ireplace('{REPLACE_STRING}', '<', $sqtmp) ;
		$todayq = str_ireplace('{REPLACE_STRING}', '=', $sqtmp) ;
		$pendingq = str_ireplace('{REPLACE_STRING}', '>', $sqtmp) ;

		$missed = $QFC->db->scalarField($missedq) ;
		$today = $QFC->db->scalarField($todayq) ;
		$pending = $QFC->db->scalarField($pendingq) ;

		$class = '' ;
		
		$total = ($missed + $today + $pending) ;
		$retTotal = $today ;

		
		$flaticon = '' ;
		if( $missed > 0 )
		{
			$class .= ' redbg' ;
			$flaticon = 'flaticon flaticon-alert' ;
		}
		else if( $today > 0 )
		{
			$class .= ' redbg' ;
			$flaticon = 'flaticon flaticon-warning' ;
		}
		else if( $today > 0 )
		{
			$class .= ' greenbg' ;
			$flaticon = 'flaticon flaticon-information' ;
		}
		else
		{
			$class .= ' whitebg' ;
			$flaticon = 'flaticon flaticon-information' ;
		}
	}
?>
<?php if($this->session->get('usr_grp_code')!= QC_USR_CUSTOMER) {?>
<li class='notification-node <?php echo $class ; ?>' >
	
	<div class="data" >
		<table>
			<caption>
				<div class="<?php echo $flaticon;?>" ></div>
				<div>Lead Appointments</div>
			</caption>
			<tr>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('lead_followups/page?appointq=missed&searchq-dt=<' . $searchdt ); ?>', {}, 'idContentAreaSmall')" >
						<b>M</b><i>issed :</i> <u><?php echo $missed; ?></u>
					</div>
				</td>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('lead_followups/page?appointq=today&searchq-dt=%3D' . $searchdt ); ?>', {}, 'idContentAreaSmall')" >
						<b>T</b><i>oday :</i> <u><?php echo $today; ?></u>
					</div>
				</td>
				<td>
					<div class="click" onclick="actionView('<?php echo siteUrl('lead_followups/page?appointq=pending&searchq-dt=>' . $searchdt ); ?>', {}, 'idContentAreaSmall')" >
						<b>F</b><i>uture :</i> <u><?php echo $pending; ?></u>
					</div>
				</td>
			</tr>
		</table>		
	</div>
</li>
<?php } ?>
<?php
	return intval($retTotal);
?>