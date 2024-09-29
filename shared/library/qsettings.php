<?php
	class Qsettings extends Library
	{		
		//properties
		public $setId ;
		public $setName ;
		public $setModule ;
		public $setUsrGrpCode ;

		public function __construct($params)
		{
			parent::__construct();
			
			$this->doAutoload() ;

			$module = false ;
			if( is_array($params) )
			{
				$module = @$params['module'] ;
			}
			$this->instance($module) ;
		}
		public function instance($module)
		{
			$gc = $this->session->get('usr_grp_code') ;
			$sql = "SELECT * FROM settings WHERE set_module='$module' AND set_usr_grp_code='$gc'" ;
			$prop = $this->db->fetchRow($sql) ;

			$this->setId = $prop['set_id'] ;
			$this->setUsrGrpCode = $prop['set_usr_grp_code'] ;
			$this->setModule = $prop['set_module'] ;
			$this->setName = $prop['set_name'] ;
		}
		public function minstance($module, $group)
		{
			$gc = $group ;
			$sql = "SELECT * FROM settings WHERE set_module='$module' AND set_usr_grp_code='$gc'" ;
			$prop = $this->db->fetchRow($sql) ;

			$this->setId = $prop['set_id'] ;
			$this->setUsrGrpCode = $prop['set_usr_grp_code'] ;
			$this->setModule = $prop['set_module'] ;
			$this->setName = $prop['set_name'] ;
		}
		function save($name, $value)
		{
			$name = strtolower($name) ;
			$userId = $this->session->get('usr_id') ;
			//delete ... then insert...
			$sql = "SELECT us_value FROM user_settings WHERE us_set_id='{$this->setId}' AND us_emp_id='$userId' AND LOWER(us_param)='$name'" ;

			$us_value = $this->db->scalarField($sql) ;
			$sqlu = '' ;
			if( $us_value === false )
			{
				$sqlu = "INSERT INTO user_settings(us_set_id, us_emp_id, us_param, us_value) VALUES('{$this->setId}', '$userId', '$name', '$value')" ;
			}
			else
			{
				$sqlu = "UPDATE user_settings SET us_value='$value' WHERE us_set_id='{$this->setId}' AND us_emp_id='$userId' AND us_param='$name'" ;
			}
			return $this->db->execute($sqlu) ;
		}
		function saveAll($nv)
		{
			$nvlist = implode("','", array_keys($nv)) ;
			if( $nvlist )
			{
				$nvlist = "'" . $nvlist . "'" ;
			}
			else
			{
				$nvlist = "''" ;
			}
			
			$this->db->beginTrans() ;
			
			$userId = $this->session->get('usr_id') ;
			//delete ... then insert...
			$sql = "DELETE FROM user_settings WHERE us_set_id='{$this->setId}' AND us_emp_id='$userId' AND LOWER(us_param) IN ($nvlist)" ;
			if( $this->db->execute($sql) )
			{

				$sqlu = '' ;
				foreach( $nv as $k => $v )
				{
					$sqlu = "INSERT INTO user_settings(us_set_id, us_emp_id, us_param, us_value) VALUES('{$this->setId}', '$userId', '$k', '$v')" ;
					$this->db->execute($sqlu) ;
					if( $this->db->lastErrorCode() )
					{
						$this->db->rollbackTrans() ;
						return false ;
					}
				}
			}
			$this->db->commitTrans() ;
			return true ;
		}
		function read($name, $uid = null )
		{
			$name = strtolower($name) ;
			$whose = ( ($uid) ? $uid : $this->session->get('usr_id')) ;
			$usrsz = " AND us_emp_id='$whose' " ;
			$sql = "SELECT us_value FROM user_settings WHERE us_set_id='{$this->setId}' $usrsz AND LOWER(us_param)='$name'" ;
			return $this->db->scalarField($sql) ;
		}
		function readAll($uid=null)
		{
			$whose = ( ($uid) ? $uid : $this->session->get('usr_id')) ;
			$usrsz = " AND us_emp_id='$whose' " ;
			$sql = "SELECT * FROM user_settings WHERE us_set_id='{$this->setId}' $usrsz " ;
			return $this->db->fetchRowSet($sql) ;
		}
		function readKV($key, $value)
		{
			$whose = $this->session->get('usr_id') ;
			$usrsz = " AND us_emp_id='$whose' " ;
			$sql = "SELECT * FROM user_settings WHERE us_set_id='{$this->setId}' $usrsz " ;
			return $this->db->fetchKV($sql, $key, $value) ;
		}
		function readOther($name, $whose = null, $hisGrp = null, $module = null)
		{
			$gc = ( ($hisGrp) ? $hisGrp : $this->session->get('usr_grp_code') ) ;
			$sql = "SELECT * FROM settings WHERE set_module='$module' AND set_usr_grp_code='$gc'" ;
			$prop = $this->db->fetchRow($sql) ;

			$setId = $prop['set_id'] ;

			$name = strtolower($name) ;
			if( ! $whose )
			{
				$whose = $this->session->get('usr_id') ;
			}
			$usrsz = " AND us_emp_id='$whose' " ;
			$sql = "SELECT us_value FROM user_settings WHERE us_set_id='{$setId}' $usrsz AND LOWER(us_param)='$name'" ;
			return $this->db->scalarField($sql) ;
		}
	}
?>