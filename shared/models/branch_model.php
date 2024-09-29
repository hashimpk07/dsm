<?php

class Branch_model extends Model
{
	public $id;
	public $name;		
	public $code;		
	public $screen_id;
	public $city_id;
	public $deleted;
	
	/* Branch Model */
    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
	function getDetails($id)
	{
		$sql = "SELECT b.code, c.id AS country_id, st.id AS state_id, ct.id AS city_id, b.id, b.name, s.name as screen, s.id as screen_id, c.name AS country, st.name AS state, ct.name AS city FROM branch b
				LEFT JOIN screen s ON s.id = b.screen_id
				LEFT JOIN city ct ON ct.id = b.city_id
				LEFT JOIN state st ON st.id = ct.state_id
				LEFT JOIN country c ON c.id = st.country_id
				WHERE b.id='$id'" ;

		return $this->db->fetchRow($sql) ;
	}
}