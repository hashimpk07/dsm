<?php

class Content_lang_model extends Model
{
	public $id ;
	public $content_id ;
	public $lang_id ;
	public $data ;
	
	/* Branch Model */
    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
	function getKVByContentId($contentId)
	{
		$sql = "SELECT cl.*, l.font, l.dir FROM content_lang cl
				INNER JOIN language l ON l.id = cl.lang_id WHERE content_id='$contentId'" ;
		$records = $this->db->fetchRowSet($sql, 'assoc') ;
		$new = array();
		foreach( $records as $k => $v )
		{
			$new[$v['lang_id']] = $v ;
		}
		return $new ;
	}
}