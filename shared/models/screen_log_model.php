<?php
class Screen_log_model extends Model
{
	public $id ;
	public $branch_id ;
	public $screen_id ;
	public $up_dt ;
	public $ping_dt ;

    function __construct() 
    {
        parent::__construct();
    }
}