<?php
	class Qprivileges extends Library
	{
		public function __construct($params)
		{
			parent::__construct();
			
			$this->doAutoload() ;
		}
		function check($codeset)
		{
			if( ! $codeset )
			{
				return false ;
			}
			$condstr = '' ;
			foreach( $codeset as $code )
			{
				$code = trim($code) ;
				$code = strtolower($code) ;
			
				if( $condstr )
				{
					$condstr .= ' OR ' ;
				}
				$condstr .= "LOWER(p.code) LIKE '%~$code~%'" ;
			}
			if($condstr) 
			{
				$condstr = ' AND ( ' . $condstr . ' ) ' ;
			}

			$usr_grp_code = $this->session->get('usr_grp_code') ;

			if( strtoupper($usr_grp_code) == 'S' || strtoupper($usr_grp_code) == '1' )
			{
				return true ;
			}
			$emp_id = $this->session->get('usr_id') ;
			if( ! $emp_id )
			{
				return false ;
			}
			//
			$sql = "SELECT COUNT(*) as total FROM privilege p
					INNER JOIN user_privilege u ON u.privilege_id = p.id $condstr AND u.user_id='$emp_id'" ;

			$ret = $this->db->scalarField($sql) ;
			if( $ret > 0 )
			{
				return true ;
			}
			return false ;
		}
	};
?>