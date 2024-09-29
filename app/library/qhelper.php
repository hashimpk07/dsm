<?php
	class Qhelper extends Library
	{
		public function __construct($params)
		{
			parent::__construct();
			
			$this->doAutoload() ;
		}
		function installDefaultPrivileges($usrId, $code)
		{
			$qi = "INSERT INTO employee_privileges(ep_emp_id, ep_priv_id) (SELECT $usrId, priv_id FROM privileges WHERE priv_usr_grp_code='$code' AND priv_default='1') ;" ;
			return $this->db->execute($qi) ;
		}
	}
?>