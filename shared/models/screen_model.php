<?php
class Screen_model extends Model
{
	public $id ;
	public $name ;
	public $w ;
	public $h ;
	public $deleted;
	public $background;
	public $branch_id;
	public $refresh_dt;

    function __construct() 
    {
        parent::__construct();
    }


	function getWindowDetails($id)
	{
		$sql = "SELECT s.background as sbackground, s.w as sw, s.h as sh, w.x as wx, w.y as wy, w.w as ww, w.h as wh, 
			w.font_weight, w.font_style, w.text_decoration, w.id as window_id, w.name as window_name, text_color, w.background, font_family, font_size FROM screen s 
				LEFT JOIN window w ON w.screen_id = s.id WHERE s.id = '$id'" ; 
		return $this->db->fetchRowSet($sql, 'assoc') ;
	}
	function getDetails($id)
	{
		$sql = "SELECT s.*, b.name as branch FROM screen s 
			LEFT JOIN branch b ON b.id = s.branch_id WHERE s.id = '$id'" ; 
		return $this->db->fetchRow($sql, 'assoc') ;
	}
}