<?php
class Window_model extends Model
{
	public $id ;
	public $name ;
	public $screen_id ;
	public $w ;
	public $h ;
	public $x ;
	public $y ;
	public $background ;
	public $text_color ;
	public $font_family ;
	public $font_size ;
	public $font_weight ;
	public $font_style ;
	public $text_decoration ;

    function __construct() 
    {
        parent::__construct();
    }
}