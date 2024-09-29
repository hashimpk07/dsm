<?php

	class Dashboard extends Controller
	{
		function __construct()
		{
			$acls = array(
				'allow' => array(
					'*' => '*' ),
				'deny' => array(),
				'order' => 'AD', //Allow, Then Deny (Options are "DA" or "AD")
				'user' => false, //Allow, Then Deny (Options are "DA" or "AD")
			);
			$this->acl($acls) ;

			parent::__construct();
		}
		function index()
		{
			$this->setPageTitle('DMS') ;
			$this->layout->loadView('dashboard');
		}
		function board()
		{
			$this->loadView('dashboard');
		}
		function scheduler($date = null)
		{
			$this->loadView('widget_schedule.php', array('SCHEDULE_DATE' => $date)) ;
		}
		function facilitydetails($code)
		{
			$occupied = false ;
			$agId = false;

			$sqlid = "SELECT fac_id FROM facilities f WHERE fac_map='$code' " ;
			$facId = $this->db->scalarField($sqlid) ;
			if( ! $facId )
			{
				echo '<p style="text-align:center">Invalid Request</p>' ;
				die;
			}

			$rec = $this->getModel('agreement_feature_model')->getAvailability($facId, DATE('Y-m-d H:i:s')) ;

			if( $rec )
			{
				$occupied = true ;
				$agId = $rec['af_ag_id'] ;
			}

			//find last agreement id
			if( ! $occupied )
			{
				$sqlag = "SELECT af_ag_id, af_last_dt FROM agreement_feature WHERE af_feat_id = '$facId' AND af_feat_type = 'F' ORDER BY af_last_dt DESC LIMIT 1 " ;
				$agId = $this->db->scalarField($sqlag) ;
			}
			$agStr = '' ;
			if( $agId )
			{
				$agStr = " AND a.ag_id='$agId' " ;
			}

			//occupied ?
			$sql1 = "SELECT f.*, af.*, a.ag_id, c.cus_id, c.cus_name, ft.ft_name, TIMESTAMPDIFF(MINUTE, NOW(), af_end_dt) as diff_mins, DATEDIFF(NOW(), af_end_dt) as diff_days FROM facilities f
			LEFT JOIN agreement_feature af ON af.af_feat_id= f.fac_id AND af.af_feat_type = 'F'
			LEFT JOIN agreement a ON a.ag_id = af.af_ag_id
			LEFT JOIN facility_types ft ON ft.ft_id = f.fac_type_id
			LEFT JOIN customers c ON c.cus_id = a.ag_cus_id
			WHERE f.fac_id='$facId' $agStr " ;

			$details = $this->db->fetchRow($sql1) ;

			$this->loadView( 'widget_floor_popup', array('occupied' => $occupied, 'result' => $details) ) ;
		}
		function scheduledetails($id)
		{
			$sql1 = "SELECT sh.*, c.cus_id, c.cus_name, f.fac_name, ft.ft_name, a.ag_id FROM schedule sh
				INNER JOIN customers c ON c.cus_id = sh.sh_cus_id
				INNER JOIN facilities f ON f.fac_id=sh.sh_fac_id
				LEFT JOIN agreement a ON a.ag_id = sh.sh_ag_id
				LEFT JOIN facility_types ft ON ft.ft_id = f.fac_type_id WHERE sh_id='$id'" ;
			$details = $this->db->fetchRow($sql1) ;

			$this->loadView( 'widget_schedule_popup', array('result' => $details) ) ;
		}
	}

?>