<?php

class Login extends Controller
{
	function __construct()
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

		$this->loadModel('user_model.php') ;
	}
	function index()
	{
		//has allraedy loged in ?
		$grpCode = $this->session->get('usr_grp_code') ;
		$usrId = $this->session->get('usr_id') ;

		if( $usrId && ($grpCode == 'S' || $grpCode == 'E' || $grpCode == 'C' ) )
		{
			redirect('dashboard') ;
			return ;
		}
		$this->layout->setLayout('black/login.php');
		$this->layout->loadView('login');
	}
	function onLogin()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		if (! $username)
		{
			$status = array(
				'status' => 'error',
				'view' =>'Enter your username',
				) ;
			jsonResponse($status) ;
		}
		else if (! $password)
		{
			$status = array(
				'status' => 'error',
				'view' =>'Enter your password',
				) ;
			jsonResponse($status) ;
		}
		else
		{
			if( defined('QFC_CUSTOMER') )
			{
				$fetch = $this->customers_model->doLogin($username, $password) ;
			}
			else
			{
				$fetch = $this->user_model->doLogin($username, $password) ;
			}

			if ($fetch)
			{
				if($this->input->post('loginkeeping'))
				{
					setcookie("username",$username);
					setcookie("password",$password);	
				}
				//get admin reseller
				$this->session->set('usr_branch', $fetch['branch_id']);
				$this->session->set('usr_grp_code', $fetch['usr_grp_code']);
				$this->session->set('usr_id', $fetch['usr_id']);
				$this->session->set('usr_alias', $fetch['usr_username']);

				$status = array(
				'status' => 'success',
				'view' => 'Login Successful',
				) ;
				jsonResponse($status) ;
			}
			else
			{
				$status = array(
				'status' => 'error',
				'view' => 'Incorrect Username or Password',
				) ;

				jsonResponse($status) ;
			}
		}
	}
	function logout()
	{
		$this->session->destroy() ;
		//load contents of login page.
		$url = siteUrl('login') ;
		$this->statusResponse('OK', 'You have been logged out.', array('__script' => "window.location='$url';")) ;
	}
}
?>