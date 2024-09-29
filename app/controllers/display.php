<?php
class Display extends Controller
{
	//Constructor
	public function __construct()
	{
		$acls = array(
			'allow' => array(
						'*' => '*' ),
			'deny' => array(),
			'order' => 'AD', //Allow, Then Deny (Options are "DA" or "AD")
			'user' => false, //Allow, Then Deny (Options are "DA" or "AD")
		);
		$this->acl($acls) ;
		parent::__construct() ;

		$this->loadModel('content_lang_model') ;
		$this->layout->theme = 'tti/screen.php' ;
	}
	function d($branch)
	{
		$sql = "SELECT id FROM branch WHERE code='$branch' AND deleted= 0 LIMIT 1 " ;
		$branch = $this->db->scalarField($sql) ;

		$sql = "SELECT screen_id FROM branch WHERE id='$branch' AND deleted= 0 LIMIT 1 " ;
		$screenId = $this->db->scalarField($sql) ;
		
		//QC_LIMIT check { 
		$sql_limit = "SELECT id FROM branch WHERE deleted= 0 ORDER BY id " ;
		$branches = $this->db->fetchRowSet($sql_limit) ;
		$i = 0 ;
		foreach( $branches as $one )
		{
			$i ++ ;
			if( $one['id'] == $branch )
			{
				if( $i > QC_LIMIT || ($i*2) > QC_LIMIT_2X )
				{
					die;
				}
			}
		}
		//} QC_LIMIT_2X -----

		//{Verify IP
		$session = get_branch_session() ;
		if( ! $session )
		{
			die;
		}
		$sqlu = "UPDATE branch SET session='" . $session . "' WHERE id='$branch' LIMIT 1" ;
		$this->db->execute($sqlu) ;
		//}

		$data = array('screen_id' => $screenId, 'branch_id' => $branch) ;
		
		$this->ping($branch, $screenId, true) ;

		$this->layout->loadView('display_d', $data) ;
	}
	function ping($branchId, $screenId, $up = false)
	{
		$col = 'ping_dt' ;
		if( $up )
		{
			$col = 'up_dt' ;
		}
		$sql = "SELECT COUNT(*) as cnt FROM screen_log WHERE screen_id='$screenId' AND branch_id='$branchId' AND DATE(up_dt)=CURDATE()" ;
		$has = $this->db->scalarField($sql) ;
		if( $has )
		{
			$data = array(
				$col => sqlNoQuote('NOW()')
			) ;
			$this->loadModel('screen_log_model')->update($data, array( sqlNoQuote('DATE(up_dt)') => sqlNoQuote('CURDATE()'), 'screen_id' => $screenId, 'branch_id' => $branchId)) ;
		}
		else
		{
			$data = array(
				$col => sqlNoQuote('NOW()'),
				'branch_id' => $branchId,
				'screen_id' => $screenId,
				'up_dt' => sqlNoQuote('NOW()'),
			) ;
			$this->loadModel('screen_log_model')->insert($data) ;
		}		
	}
	function query_cache($branchId)
	{
		$session = get_branch_session() ;
		$sqla = "SELECT screen_id FROM branch WHERE session='$session' AND deleted ='0' AND id='$branchId' LIMIT 1 " ;
		$screenId = $this->db->scalarField($sqla) ;

		if( ! $screenId )
		{
			return ;
		}
		$sql = "SELECT c.* FROM telecast sh
				INNER JOIN window w ON w.id = sh.window_id
				INNER JOIN screen s ON s.id = w.screen_id 
				INNER JOIN content c ON c.id = sh.content_id
				WHERE c.approved=1 AND ( c.type = 'I' OR c.type = 'IU' OR c.type = 'V' OR c.type = 'VU' ) 
					AND CURDATE() BETWEEN DATE(from_dt) AND DATE(to_dt) AND ( (TIMESTAMPDIFF(MINUTE, NOW(), from_dt)  BETWEEN -1000 AND 6000 ) )
				AND s.id = '$screenId'" ;
	
		$data = $this->db->fetchRowSet($sql) ;
		foreach( $data as $k => $rec )
		{
			$lang_data = $this->content_lang_model->getKVByContentId($rec['id']) ;

			foreach( $lang_data as $k => $v )
			{
				$lang_data[$k]['checksum'] = 'i' . md5($v['data']) ;

				if( $rec['type'] == 'I' || $rec['type'] == 'V' )
				{
					$lang_data[$k]['data'] = baseUrl('app/data/' . $v['data']) ;
				}
			}
			$data[$k]['lang_data'] = $lang_data ;
		}
		foreach( $data as $k => $v )
		{
			if( isset($v['lang_data']) )
			{
				foreach( $v['lang_data'] as $k1 => $v1 )
				{
					if( $v['type'] == 'V' || $v['type'] == 'VU'  )
					{
				?>
				<video width="100%" height="100%" src="<?php echo $v1['data'];?>" ></video>
				<?php
					}
					else if( $v['type'] == 'I' || $v['type'] == 'IU'  )
					{
				?>
				<img width="100%" height="100%" src="<?php echo $v1['data'];?>" />
				<?php
					}
				}
			}
		}
	}
	function query()
	{
		$branchId = $_REQUEST['branchId'] ;
		$cur_session = get_branch_session() ;
		$sqla = "SELECT screen_id, session FROM branch WHERE deleted=0 AND id='$branchId' LIMIT 1 " ;
		$row = $this->db->fetchRow($sqla) ;

		$old_session = $row['session'] ;
		$screenId = $row['screen_id'] ;
		if( ! $screenId )
		{
			return ;
		}
		if( $cur_session != $old_session )
		{
			$sqlu = "UPDATE branch SET session='$cur_session' WHERE id='$branchId' LIMIT 1" ;
			$this->db->execute($sqlu) ;
			return ;
		}
		//ping branch..
		$this->ping($branchId, $screenId) ;
		//todo;pre load not done..

		$allowed = PRM_REQUEST_INTERVAL * 2 ;
		
		$sql = "SELECT s.background as screen_background, w.background, w.text_color, w.font_size, w.font_family, w.font_weight, w.font_style, w.text_decoration,
				sh.*, s.id as screen_id, s.w as sw, s.h as sh, w.x as wx, w.y as wy, w.w as ww, w.h as wh, 
				c.*, TIMESTAMPDIFF(SECOND, refresh_dt, NOW()) as diff, IF( TIMESTAMPDIFF(SECOND, refresh_dt, NOW()) < $allowed, 1,0) as refresh FROM telecast sh
				INNER JOIN window w ON w.id = sh.window_id
				INNER JOIN screen s ON s.id = w.screen_id 
				INNER JOIN content c ON c.id = sh.content_id
				WHERE c.approved=1 AND ( NOW() BETWEEN from_dt AND to_dt ) AND s.id = '$screenId'" ;
		$records = $this->db->fetchRowSet($sql) ;

		$sqls = "SELECT * FROM screen WHERE id='$screenId'" ;
		$screeninfo = $this->db->fetchRow($sqls, 'assoc') ;

		$data = array(
				'screen_id' => $screeninfo['id'],
				'screen_background' => $screeninfo['background'],
				'screen_unit' => QC_SCREEN_UNIT,
				'screen_w' => $screeninfo['w'],
				'screen_h' => $screeninfo['h'],
				'windows' => array(),
			);


		$_index = 0 ;
		foreach( $records as $rec )
		{
			$lang_data = $this->content_lang_model->getKVByContentId($rec['content_id']) ;

			foreach( $lang_data as $k => $v )
			{
				$lang_data[$k]['checksum'] = md5($v['data']) ;
				if( $rec['type'] == 'I' || $rec['type'] == 'V' )
				{
					$lang_data[$k]['data'] = baseUrl('app/data/' . $v['data']) ;
				}
			}
			$data['windows'][$_index] = array(
					'window_id' => $rec['window_id'],
					'window_x' => $rec['wx'] ,
					'window_y' => $rec['wy'] ,
					'window_w' => $rec['ww'] ,
					'window_h' => $rec['wh'] ,
					'window_background' => $rec['background'] ,
					'window_text_color' => $rec['text_color'] ,
					'window_font_size' => $rec['font_size'] ,
					'window_font_family' => $rec['font_family'] ,
					'window_font_weight' => $rec['font_weight'] ,
					'window_font_style' => $rec['font_style'] ,
					'window_text_decoration' => $rec['text_decoration'] ,
					'window_type' => $rec['type'],
					'window_data' => $lang_data,
				);
			
			if( $rec['refresh'] )
			{
				$data['command'] = 'REFRESH' ;
			}
			$_index ++ ;
		}
		
		jsonResponse($data) ;
	}
}
?>