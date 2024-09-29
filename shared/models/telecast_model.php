<?php
class Telecast_model extends Model
{
	public $id ;
	public $from_dt;	
	public $to_dt ;	
	public $window_id;
	public $content_id;

    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
	function getDetails($id)
	{
		$sql = "SELECT c.title, c.id as content_id, c.type, t.from_dt, t.to_dt, t.id, b.id as branch_id, s.id as screen_id, w.id as window_id, w.name as window, s.name as screen, b.name as branch FROM telecast t
				LEFT JOIN window w ON w.id = t.window_id
				LEFT JOIN screen s ON s.id = w.screen_id
				LEFT JOIN branch b ON b.id = s.branch_id
				INNER JOIN content c ON c.id = t.content_id
				WHERE t.id='$id'" ;
		return $this->db->fetchRow($sql) ;
	}
	function getDayDetails($date, $window = null, $screen = null)
	{
		$wcond = '' ;
		if( $window )
		{
			$wcond = " AND w.id='$window' " ;
		}
		$scond = '' ;
		if( $screen )
		{
			$scond = " AND s.id='$screen' " ;
		}
		$date_from = Date('Y-m-d', strtotime($date)) ;

		$sql = "SELECT t.id, c.type, w.name AS window, t.*, '$date_from' as query_date FROM telecast t 
				INNER JOIN window w ON w.id = t.window_id 
				LEFT JOIN screen s ON s.id = w.screen_id
				LEFT JOIN content c ON c.id = t.content_id
				WHERE c.approved=1 AND DATE(t.from_dt) <= '$date_from' AND DATE(t.to_dt) >= '$date_from' $wcond $scond " ;
		$records = $this->db->fetchRowSet($sql) ;
		
		return $records ;
	}
	function getTimeline($date, $window = null, $screen = null)
	{
		$recset = $this->getDayDetails($date, $window, $screen ) ;
		return $this->rowToTimeline($recset) ;
	}
	function rowToTimeline($rows)
	{
		$obj = $this->loadModel('content_type_model') ;

		$timeline_set = array() ;
		foreach($rows as $one )
		{
			$timeline = array() ;
			$timeline['window_id'] = $one['window_id'] ;
			$timeline['from_dt'] = $one['from_dt'] ;
			$timeline['to_dt'] = $one['to_dt'] ;
			$timeline['window'] = $one['window'] ;
			$timeline['id'] = $one['id'] ;
			
			$from_time = Date('H:i:s', strtotime($one['from_dt'])) ;
			$to_time = Date('H:i:s', strtotime($one['to_dt'])) ;
			
			$f = '1-1-1970 ' . $from_time ;
			$t = '1-1-1970 ' . $to_time ;
			$m = '1-1-1970 23:59:59' ;
			$from_ts = strtotime($f) ;
			$to_ts = strtotime($t) ;
			$max_ts = strtotime($m) ;
			
			$from_ts += TIMEZONE_DIFF_SECONDS ;
			$to_ts += TIMEZONE_DIFF_SECONDS ;
			$max_ts += TIMEZONE_DIFF_SECONDS ;

			$from_ts /= 100 ;
			$to_ts /= 100 ;
			$max_ts /= 100 ;
			
			$timeline['from'] = $from_ts ;
			$timeline['to'] = ($to_ts - $from_ts) ;
			$timeline['max'] = $max_ts ;
			$timeline['color'] = $obj->color($one['type']) ;
			$timeline['content_type'] = $obj->explain($one['type']) ;
			
			$timeline_set[$timeline['window_id']][]	 = $timeline ;
		}
		return $timeline_set ;
	}
}