<?php
class Cron extends Controller
{
	public function __construct()	{

		$acls = array(
			'allow' => array(
						'*' => '*' ),
			'deny' => array(),
			'order' => 'AD', //Allow, Then Deny (Options are "DA" or "AD")
		);

		$this->acl($acls) ;
		parent::__construct();
	}
	public function postBill($dt=null)
	{
		try
		{
			postBill($dt) ;
		}
		catch(Exception $e )
		{
			//push error
			$dtstr = clientDate( ($dt) ? $dt : Date('Y-m-d H:i:s') ) ;
			$msg = "Post Bill Cron not succesfully run on $dtstr." ;
			$sql = "UPDATE parameters SET pm_html='$msg'" ;
			$this->db->execute($sql) ;
		}
	}
	public function preBill($dt=null)
	{
		try
		{
			preBill($dt) ;
		}
		catch(Exception $e )
		{
			//push error
			$dtstr = clientDate( ($dt) ? $dt : Date('Y-m-d H:i:s') ) ;
			$msg = "Pre Bill Cron not succesfully run on $dtstr." ;
			$sql = "UPDATE parameters SET pm_html='$msg'" ;
			$this->db->execute($sql) ;
		}
	}
	function process_outbox($fail = 0)
	{
		$cond = '' ;
		if( $fail )
		{
			$cond .= " WHERE out_fail != 0 " ;
		}
		else
		{
			$cond .= " WHERE out_fail = 0 " ;
		}
		$sqla = "SELECT * FROM outbox $cond " ;
		$set = $this->db->fetchRowSet($sqla) ;

		foreach( $set as $one )
		{
			$status = true ;
			if( $one['out_type'] == QC_MSGTYPE_SMS )
			{
				$to = $one['out_to'] ;
				$msg = $one['out_message'] ;
				
				$status = sendCurlSms($msg, $to) ;
			}
			else if( $one['out_type'] == QC_MSGTYPE_EMAIL )
			{
				$to = $one['out_to'] ;
				$msg = $one['out_message'] ;
				$subject = $one['out_subject'] ;
				
				$status = sendSmtpMail($subject, $msg, $to) ;
			}
			if( ! $status )
			{
				$sqlu = "UPDATE outbox SET out_fail=out_fail+1 WHERE out_id = '$one[out_id]' LIMIT 1 " ;
				$this->db->execute($sqlu) ;
			}
			else
			{
				$sqld = "DELETE FROM outbox WHERE out_id = '$one[out_id]' LIMIT 1 " ;
				$this->db->execute($sqld) ;
			}
		}
	}
}
?>