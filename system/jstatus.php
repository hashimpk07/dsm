<?php
	class JStatus
	{
		/**
		 * operation id.
		 * 
		 * @var string
		 */
		public $id ;
		/**
		 * Status OK, or FAIL
		 * 
		 * @var strig
		 */
		public $status ;
		
		/**
		 * Message
		 * @var string
		 */
		public $msg ;
		/**
		 * Other data set.
		 * @var array 
		 */
		public $data ;
		
		/**
		 * construct with error info
		 * @param type $status
		 * @param type $msg
		 * @param type $data
		 * @param type $op
		 */
		function __construct($status = null, $msg = null, $data = null, $op = null)
		{
			$this->status = $status ;
			$this->msg = $msg ;
			$this->data = $data ;
			$this->op = $op ;
		}
		/**
		 * set error info
		 * @param type $status
		 * @param type $msg
		 * @param type $data
		 * @param type $op
		 */
		function set($status = null, $msg = null, $data = null, $op = null)
		{
			$this->status = $status ;
			$this->msg = $msg ;
			$this->data = $data ;
			$this->op = $op ;
		}
		/**
		 * clear status data..
		 */
		function clear()
		{
			$this->status	= null ;
			$this->msg		= null ;
			$this->data		= null ;
			$this->op		= null ;
		}
	};
?>