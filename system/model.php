<?php
/**
 * Model base class
 */
class Model extends MVC
{

	/**
	 * Keep current table name 
	 * 
	 * @var string table name
	 */
	public $__table;
	public $__insert_id ;
	public $__pkey = 'id' ;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->__table = str_ireplace('_model', '', strtolower(get_class($this)));
		
		parent::__construct() ;
	}

	/**
	 * Set table name.
	 * 
	 * @param type $table
	 */
	function setTable($table)
	{
		$this->__table = $table;
	}

	/**
	 * Get table name.
	 * 
	 * @return string table name
	 */
	function getTable()
	{
		return $this->__table;
	}
	function insert($data, $tobj=null)
	{
		if( $tobj == null )
		{
			$tobj = $this ;
		}

		$di = array() ;
		foreach( $data as $k => $v )
		{
			if(is_object($tobj->{$k}) )
			{
				if( $this->insert($v, $this->{$k}) )
				{
					$v = $this->{$k}->db->getLastInsertId() ;
				}
				else
				{
					$v = sqlNoQuote('NULL') ;
				}
			}
			$di[$k] = $v ;
		}
		//Insert
		return $this->insertSingle($di, $tobj) ;
	}
	/**
	 * Insert a record into datbase.
	 * 
	 * @param array $data record to insert
	 * @return bool 
	 */
	function insertSingle($data, $table=null)
	{
		if( ! $table )
		{
			$table = $this ;
		}
		$sql = "INSERT INTO `{$table->__table}` " ;
		$keys = "`" . implode("`,`", array_keys($data)) . "`" ;
		
		$valuestr = '' ;
		foreach( $data as $v )
		{
			if( $valuestr )
			{
				$valuestr .= ', ' ;
			}
			if( strpos($v, QFS_SQLNOQUOTE_TOKEN) === 0 )
			{
				$valuestr .= str_replace(QFS_SQLNOQUOTE_TOKEN, '', $v) ;
			}
			else
			{
				$valuestr .= "'" . $v . "'" ;
			}
		}

		$sql .= '(' . $keys . ')' ;
		$sql .= ' VALUES(' . $valuestr. ')' ;
		return $this->db->execute($sql) ;
	}
	/**
	 * Delete specified records.
	 * 
	 * @param array | string $where where condition array or string
	 * @return bool 
	 */
	function delete($where)
	{
		//NOTE;There is not related deletion. Please set delete cascade in database table.
		$whereStr = $this->buildWhereClause($where) ;
		$sql = "DELETE FROM `{$this->__table}` $whereStr" ;
		if( $this->db->execute($sql) )
		{
			return $this->db->affectedRows() ;
		}
		return false ;
	}
	/**
	 * Change a flag value in a table.
	 * 
	 * @param type $flagColumn flag column name.
	 * @param type $flagValue new flag value.
	 * @param type $where where condition array or string.
	 */
	function flag($flagColumn, $flagValue, $where)
	{
		if( strpos($flagValue, QFS_SQLNOQUOTE_TOKEN) === 0 )
		{
			$flagValue .= str_replace(QFS_SQLNOQUOTE_TOKEN, '', $flagValue) ;
		}
		else
		{
			$flagValue = "'" . $flagValue . "'" ;
		}

		$sql = "UPDATE `{$this->__table}` SET `$flagColumn`= $flagValue " ;
		$whereStr = $this->buildWhereClause($where) ;
		$sql = $sql . ' ' . $whereStr ;
		if( $this->db->execute($sql) )
		{
			return $this->db->affectedRows() ;
		}
		return false ;		
	}
	/**
	 * Update records
	 * 
	 * @param array $data data to update
	 * @param array | string $where where array or where condition
	 * @return bool true or false
	 */
	function updateSingle($data, $where, $table = null)
	{
		if( ! $table )
		{
			$table = $this ;
		}
		$sql = "UPDATE `{$table->__table}` " ;
		
		$u = '' ;
		foreach( $data as $k => $v )
		{
			if( $u )
			{
				$u .= ', ' ;
			}
			$va = $v ;
			if( strpos($va, QFS_SQLNOQUOTE_TOKEN) === 0 )
			{
				$va = str_replace(QFS_SQLNOQUOTE_TOKEN, '', $va) ;
			}
			else
			{
				$va = "'" . $va . "'" ;
			}
			//check k too
			if( strpos($k, QFS_SQLNOQUOTE_TOKEN) === 0 )
			{
				$k = str_replace(QFS_SQLNOQUOTE_TOKEN, '', $k) ;
			}

			$u .= "$k = $va" ;
		}
		$sql .= ' SET ' . $u ;
		$sql .= ' ' . $this->buildWhereClause( $where ) ;

		return $this->db->execute($sql) ;
	}
	//Cacade update will work with child tables having primary key.
	function update($data, $where, $tobj=null)
	{
		if( $tobj == null )
		{
			$tobj = $this ;
		}

		$di = array() ;
		foreach( $data as $k => $v )
		{
			if(is_object($tobj->{$k}) )
			{
				if( $this->update($v, array($this->{$k}->__pkey => $v[$this->{$k}->__pkey] ), $this->{$k}) )
				{
					$v = $v[$this->{$k}->__pkey] ;
				}
				else
				{
					$v = '' ;
				}
			}
			$di[$k] = $v ;
		}
		//Insert
		return $this->updateSingle($di, $where, $tobj) ;
	}
	/**
	 * 
	 * @param type $where
	 * @param type $limit no of records to fetch
	 * @return array records
	 */
	function get($limit = null, $type = 'assoc')
	{
		return $this->getWhere(null, null, $limit, $type);
	}
	function getOne($type='assoc')
	{
		$res = $this->get(1, $type) ;
		if(is_array($res))
		{
			$first = reset($res) ;
			return $first ;
		}
		return false ;
	}

	/**
	 * 
	 * @param type $where
	 * @param type $orderBy order by fields
	 * @param type $limit no of records to fetch
	 * @return array records
	 */
	function getBy($orderBy = null, $limit = null, $type = 'assoc')
	{
		return $this->getWhere(null, $orderBy, $limit, $type);
	}
	function getByOne($orderBy = null, $type = 'assoc')
	{
		return $this->getWhereOne(null, $orderBy, $type);
	}
	function getWhere($where = null, $orderBy = null, $limit = null, $type = 'assoc')
	{
		return $this->getWhereBy($where, $orderBy, $limit, $type) ;
	}
	function getWhereOne($where = null, $orderBy = null, $type = 'assoc')
	{
		return $this->getWhereByOne($where, $orderBy, $type) ;
	}
	/**
	 * 
	 * @param type $where
	 * @param type $orderBy order by fields
	 * @param type $limit no of records to fetch
	 * @return array records
	 */
	function getWhereBy($where = null, $orderBy = null, $limit = null, $type = 'assoc')
	{
		$sql = "SELECT * FROM `" . $this->__table . "`";

		$whereStr = $this->buildWhereClause($where);
		$orderByStr = '';

		$sql .= $whereStr ;
		if ($orderBy)
		{
			$orderByStr = "ORDER BY " . $orderBy;
			$sql .= ' ' . $orderByStr ;
		}
		if ($limit)
		{
			$sql .= " LIMIT $limit" ;
		}
		
		return $this->db->fetchRowSet($sql, $type);
	}
	/**
	 * 
	 * @param type $where
	 * @param type $orderBy order by fields
	 * @param type $limit no of records to fetch
	 * @return array records
	 */
	function getWhereByOne($where = null, $orderBy = null, $type = 'assoc')
	{
		$sql = "SELECT * FROM `" . $this->__table . "`";

		$whereStr = $this->buildWhereClause($where);
		$orderByStr = '';

		$sql .= $whereStr ;
		if ($orderBy)
		{
			$orderByStr = "ORDER BY " . $orderBy;
			$sql .= ' ' . $orderByStr ;
		}
		
		$sql .= " LIMIT 1" ;
		
		return $this->db->fetchRow($sql, $type);
	}
	function buildWhereClause($where)
	{
		$whereStr = '' ;
		//build where... {
		if( is_array($where) )
		{
			foreach( $where as $k => $v )
			{
				if( $whereStr )
				{
					$whereStr .= ' AND ' ;
				}
				//check value..
				if( strpos($k, QFS_SQLNOQUOTE_TOKEN) === 0 )
				{
					$va = str_replace(QFS_SQLNOQUOTE_TOKEN, '', $v) ;
				}
				else
				{
					$va = "'$v'" ;
				}
				//check key..
				if( strpos($k, QFS_SQLNOQUOTE_TOKEN) === 0 )
				{
					$k = str_replace(QFS_SQLNOQUOTE_TOKEN, '', $k) ;
				}
				$whereStr .= " $k=$va " ;
			}
		}
		else if( $where )
		{
			$whereStr = $where ;
		}
		//}
		if( $whereStr )
		{
			$whereStr = ' WHERE ' . $whereStr ;
		}
		return $whereStr ;
	}
	/**
	 * Load a record by condition
	 * 
	 * @param array | string $where conditions
	 */
	function load($where)
	{
		$whereStr = $this->buildWhereClause($where);
		$sql = "SELECT * FROM `" . $this->__table . "` " . $whereStr;
		return $this->loadSql($sql);
	}
	/**
	 * Load record using sql query.
	 * 
	 * @param string $sql sql query
	 */
	function loadSql($sql)
	{
		$record = $this->db->fetchRow($sql);
		if (is_array($record))
		{
			return $record;
		}
		return false;
	}
	function isExists($where)
	{
		$whereStr = $this->buildWhereClause($where);
		$sql = "SELECT COUNT(*) as total FROM `" . $this->__table . "` $whereStr" ;
		
		$count = $this->db->scalarField($sql) ;
		if( $count > 0 )
		{
			return true ;
		}
		return false ;
	}
}
/**
 * Model for handliing property set
 */
class PropertyModel 
{
	/*
	 * Data collection
	 */
	public $collection ;
	
	/**
	 * Return complete perperty set.
	 * 
	 * @return array
	 */
	function all()
	{
		return $this->collection ;
	}
	/**
	 * Exclude specified elements by matching keys
	 * 
	 * @param array $keySet array of keys to exclude
	 */
	function excludes($keySet)
	{
		$retSet = array() ;
		foreach( $keySet as $k => $v )
		{
			if( isset($this->collection[$k]) )
			{
				continue;
			}
			$retSet[$k] = $v ;
		}
		return $retSet ;
	}
	/**
	 * Include specified elements by matching keys
	 * 
	 * @param array $keySet array of keys to include
	 */
	function includes($keySet)
	{
		$retSet = array() ;
		foreach( $keySet as $k => $v )
		{
			if( isset($this->collection[$k]) )
			{
				$retSet[$k] = $v ;
			}
		}
		return $retSet ;
	}
	/**
	 * Check key exists or not.
	 * 
	 * @param string $key key to match
	 * @return boolean true on success
	 */
	function hasKey($key)
	{
		if( isset($this->collection[$key]) )
		{
			return true ;
		}
		return false ;
	}
	/**
	 * Check value exists or not.
	 * 
	 * @param string $val value to check
	 * @return boolean true on success
	 */
	function has($val)
	{
		if( array_search($val, $this->collection) === false )
		{
			return false ;
		}
		return true ;
	}
	/**
	 * Get value of specified key.
	 * 
	 * @param string $key
	 * @return string value of the specified key
	 */
	function explain($key)
	{
		if( isset($this->collection[$key]) )
		{
			return $this->collection[$key] ;
		}
		return false ;
	}
}
/**
 * Virtual Model base class
 */
class VirtualModel 
{
	
}

?>