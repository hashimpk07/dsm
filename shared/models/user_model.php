<?php

class User_model extends Model
{
	public $id ;
	public $name ;	
	public $username ;	
	public $password ;	
    public $branch_id ;	
	public $user_group_id ;
	public $createby ;
	public $phone ;
	public $email ;
	public $designation ;
	public $remarks ;
	public $createdt ;
	public $updateby ;
	public $updatedt ;

    function __construct() 
    {
        parent::__construct();
		
		$this->__pkey = 'id' ;
    }
     function getUsers()
     {
         $sql = "SELECT u.id as id, u.name FROM user u WHERE deleted = 0 " ;
		return $this->db->fetchRowset($sql) ;
     }
    function getUserName($id)
	{
		$sql="SELECT name FROM user u where id = '$id' " ;
		return $this->db->scalarField($sql) ;
	}
	/*function getDetails($id)
	{
		$sql = "SELECT * FROM employee WHERE id='$id' " ;
		return $this->db->fetchRow($sql) ;
	}*/
	
	function getDetails($id)
	{
		$sql = "SELECT u.*, b.name as branchname FROM user u
				LEFT JOIN branch b ON b.id = u.branch_id
				WHERE u.id='$id'" ;

		return $this->db->fetchRow($sql) ;
	}
	
	function doLogin($usr, $pwd)
    {
		$pwd = md5($pwd) ;
		
        $sql = "SELECT COUNT(*) as total, id as usr_id, username as usr_username, user_group_id as usr_grp_code, branch_id FROM user
				WHERE `username` = '$usr' AND  `password` = '$pwd' AND blocked='0' AND  deleted='0' " ; 
		$row = $this->db->fetchRow($sql) ;
		if( $row['total'] > 0 )
		{
			return $row ;
		}
		return false ;
    }
	function usernameCheck($username)
	{
		$qry = mysql_query("SELECT COUNT(*) as cnt FROM user WHERE  `username` =  '$username' AND deleted=0");
		return $this->db->scalarField($qry) ;
    }
}