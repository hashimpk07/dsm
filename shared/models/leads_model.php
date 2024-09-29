<?php

class Leads_model extends Model
{

	public $lead_id ;
	public $lead_title ;
	public $lead_name ;
		public $lead_gender ;
		public $lead_dob ;
		public $lead_imm_name ;
		public $lead_imm_phone ;
		public $lead_imm_email ;
		public $lead_desig ;
		public $lead_org_name ;
		public $lead_org_details ;
	public $lead_addr_line ;
	public $lead_addr_street ;
	public $lead_addr_city ;
	public $lead_addr_st_id ;
	public $lead_addr_cn_id ;
	public $lead_email ;
	public $lead_phone ;
	public $lead_details1 ;
	public $lead_details2 ;
	public $lead_ref_id ;
	public $lead_bk_id ;
	public $lead_bk_commission;
	public $lead_dt ; 
	public $lead_owner ;
	public $lead_remarks ;
	public $lead_score ;
	public $lead_cus_id ;
	public $lead_deleted ; 
	public $lead_createby ; 
	public $lead_createdt ;
	public $lead_updateby ;
	public $lead_updatedt ;
	
	function __construct()
	{
		parent::__construct();

		$this->__pkey = 'lead_id';
	}
	function getAsCustomer($leadId)
	{
		$sql = "SELECT lead_name AS cus_name, lead_gender AS cus_gender, lead_dob AS cus_dob, lead_imm_name AS cus_imm_name,
				lead_imm_phone AS cus_imm_phone, lead_imm_email AS cus_imm_email, lead_desig AS cus_desig, lead_org_name AS cus_org_name, 
				lead_org_details AS cus_org_details, lead_addr_line AS addr_line, lead_addr_street AS addr_street, 
				lead_addr_city AS addr_city, lead_addr_st_id AS addr_st_id, lead_addr_cn_id AS addr_cn_id,
				lead_email AS cus_email, lead_phone AS cus_phone, lead_details1 AS cus_details1, lead_details2 AS cus_details2,
				lead_ref_id AS cus_ref_id, lead_bk_id AS cus_bk_id, lead_bk_commission AS cus_bk_commission, lead_dt AS cus_dt,
				lead_remarks AS cus_remarks, lead_deleted AS cus_deleted, lead_createby AS cus_createby, lead_createdt AS cus_createdt,
				lead_updateby AS cus_updateby, lead_updatedt AS cus_updatedt
					FROM leads WHERE lead_id='$leadId'" ;
		return $this->db->fetchRow($sql) ;
	}
	
	function getDetails($id)
	{
		$sql = "SELECT CONCAT(uo.emp_name, IF(LENGTH(uo.emp_code)>0, CONCAT(' (',uo.emp_code,')'), '')) as emp_display, l.*, bk_name as lead_bk_ps_name, cn.cn_name as lead_addr_country_name, st.st_name as lead_addr_state_name, r.ref_name as lead_ref_name, uc.emp_username AS created_username, uu.emp_username AS updated_username, l.lead_createdt, l.lead_updatedt,l.lead_addr_cn_id,l.lead_addr_st_id FROM leads l
				LEFT JOIN employee AS uc ON l.lead_createby=uc.emp_id 
				LEFT JOIN employee AS uu ON l.lead_updateby=uu.emp_id
				LEFT JOIN employee AS uo ON l.lead_owner = uo.emp_id
				LEFT JOIN states AS st ON st.st_id = l.lead_addr_st_id
				LEFT JOIN countries AS cn ON cn.cn_id=l.lead_addr_cn_id
				LEFT JOIN brokers b ON b.bk_id=l.lead_bk_id
				LEFT JOIN lead_reference r ON r.ref_id =l.lead_ref_id
				WHERE l.lead_id='$id' LIMIT 1" ;

		return $this->db->fetchRow($sql) ;
	}

}