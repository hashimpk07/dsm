<?php

class Alldata extends Library
{
	public $location_id = 0 ;
	public $department_ids = array() ;

	function __construct()
	{
		global $QFA ;
		global $QFC ;
		
		$uid = $QFC->session->get('usr_id') ;
		$uc = $QFC->session->get('usr_grp_code') ;

		$sql = "SELECT location_id FROM employee WHERE id='$uid' LIMIT 1" ;
		$this->location_id = $QFC->db->scalarField($sql) ;
		$QFA['location.id'] = $this->location_id ;

		if( $this->location_id )
		{
			$location_id = $this->location_id ;
			$sqld = "SELECT * FROM department WHERE location_id='$location_id' AND deleted=0 ORDER BY `type` = 'J' ASC , `type` = 'N' DESC , `type` ASC" ;
			$departments = $QFC->db->fetchRowSet($sqld) ;
			$QFA['location.departments'] = $departments ;
		}

		if( is_array(@$departments) )
		{
			foreach( $departments as $d )
			{
				$this->department_ids[] = $d['id'] ;
			}
		}
		
		$instr = "0" ;
		$in = implode("','", $this->department_ids)  ;
		if( $in )
		{
			$instr = "'" . $in . "'" ;
		}
		$QFA['location.idset'] = $this->department_ids ;
		$QFA['location.inquery'] = $instr ;
		//unrestird for admin
		if( $uc == QC_USR_SUPERADMIN )
		{
			$QFA['location.id'] = 0 ;
		}
	}
};