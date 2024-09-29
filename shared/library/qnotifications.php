<?php
	class Qnotifications extends Library
	{
		public function __construct($params)
		{
			parent::__construct();
			
			$this->doAutoload() ;
		}
		/**
		 * Return html code for displaying notification. Contains the followign classes.
		 * .notify-item : a wrapper div for notification div.
		 * .notify-subject : div which contains notification subject.
		 * .notify-message : a div which contains notification message.
		 * @return true on success.
		 */
		function show()
		{
			$usrId = $this->session->get('usr_id') ;
			$usrGrpCode = $this->session->get('usr_grp_code') ;
			$parentUid = 0 ;
			$pstr = '' ;
			//if customer get parent id..
			if( $usrGrpCode == 'C' )
			{
				$sql = "SELECT cus_parent_emp_id FROM customers WHERE cus_emp_id='$usrId'" ;
				$parentUid = $this->db->scalarField($sql) ;
				
				$pstr = " AND (n.ntf_notify_all=1) OR (t.nt_child_id='$parentUid' AND t.nt_grandchild_flag=1)" ;
			}
			
			if( $usrGrpCode == 'R' )
			{
				$pstr = " AND (n.ntf_notify_all=1 AND (n.ntf_create_grp_code='S' OR n.ntf_create_grp_code='E') OR (t.nt_child_id='$usrId' AND t.nt_child_flag=1))" ;
			}
			else if( $usrGrpCode == 'S' || $usrGrpCode == 'E' )
			{
				$pstr = " AND ( 1 = 0 ) " ;
			}
			$sql = "SELECT * FROM notifications n
					LEFT JOIN notification_targets t ON n.ntf_id=t.nt_ntf_id WHERE ntf_deleted=0 AND n.ntf_expire_dt >= NOW() 
					$pstr " ;
			$records = $this->db->fetchRowSet($sql) ;

			if( is_array($records) )
			{
				foreach( $records as $rec )
				{
					?>
					<div class="notification-item" style="color:white;">
						<div class="subject"><?php echo $rec['ntf_subject'];?></div>
						<div class="message"><?php echo $rec['ntf_msg'];?></div>
					</div>
					<?php
				}
			}
			return true ;
		}
	}
?>