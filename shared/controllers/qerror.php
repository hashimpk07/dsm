<?php

class Qerror extends Controller
{
	public $errors = array(
		'403' => 'Access denied',
		'404' => 'The server is refusing to respond to it',
	);

	
	public function __construct()
	{
		$acls = array(
			'allow' => array(
						'*' => '*' ),
			'deny' => array(),
			'order' => 'AD', //Allow, Then Deny (Options are "DA" or "AD")
			'user' => false, //Login permisson not required.
		);
		$this->acl($acls) ;

		parent::__construct();
	}
	function display($code = 0)
	{
		$a = array( 
			'msg' => (isset($this->errors[$code]) ? $this->errors[$code] : 'Unknown error.'),
			'code' => $code,
			) ;
		$this->layout->theme = 'tti/error.php' ;
		$this->layout->loadView('qerror.php', $a);
	}

}

?>