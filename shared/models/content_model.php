<?php
class Content_model extends Model
{
	public $id ;
	public $type ;	
	public $future ;
	public $dt ;
	public $title ;
	public $approved ;
	public $classification;
	public $branch_id;
	public $approved_by ;
	public $approved_dt ; 

    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
	function getDetails($id)
	{
		$sql = "SELECT c.* FROM content c WHERE id='$id' LIMIT 1" ;
		$record = $this->db->fetchRow($sql) ;
		
		$sql2 = "SELECT cl.*, l.dir, l.name as language FROM content_lang cl
				INNER JOIN language l ON l.id = cl.lang_id WHERE content_id='$id' " ;
		$lang_data_set = $this->db->fetchRowSet($sql2) ;
		
		$lang_data = array() ;
		foreach( $lang_data_set as $v )
		{
			$lang_data[$v['lang_id']] = $v ;
		}
		$record['lang_data'] = $lang_data ;
		
		return $record ;		
	}
}