<?php
require_once 'config.php';

class Database
{
	var $database;
	var $stmt;
	function __construct()
	{
		global $hostname, $username, $password, $db_name;
		$this->database = new mysqli($hostname, $username, $password, $db_name);

		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
	}
	
	function query($sql, $params)
	{
		if(isset($this->stmt))
		{
			$this->stmt->free_result();
		}
		$this->stmt = $this->database->stmt_init();
		
		if(!$this->stmt->prepare($sql)) {
			$this->error("Could not prepare statement", $sql);
		}
		
		if(count($params) > 0)
		{
			$this->build_binding($params);
			
			call_user_func_array(array($this->stmt, 'bind_param'), $this->refValues($params));
		}
		
		if(!($this->stmt->execute()))
		{
			$this->error("Executing database query", $sql);
		}
	}
	
	function build_binding(&$params)
	{
		$binding = '';
		foreach ($params as $Param) 
		{ 
			if (is_int($Param)) 
			{ 
				$binding .= 'i'; 
			} 
			else if (is_double($Param)) 
			{ 
				$binding .= 'd'; 
			} 
			else if (is_string($Param)) 
			{ 
				$binding .= 's'; 
			} 
		}
		array_unshift($params, $binding);
	}
	
	function refValues($arr){
        if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }
	
	function bind(&$out) {
		$data = $this->stmt->result_metadata();
		$fields = array();
		$out = array();

		$count = 0;

		while($field = mysqli_fetch_field($data)) {
			$fields[$count] = &$out[$field->name];
			$count++;
		}    
		call_user_func_array(array($this->stmt, 'bind_result'), $fields);
		
		$this->stmt->store_result();
	}
	
	function fetch_row()
	{
		return $this->stmt->fetch();
	}
	
	function how_many()
	{
		return $this->stmt->num_rows;
	}
	
	function get_one_row($sql, $params)
	{
		$this->query($sql, $params);
		$result = array();
		$this->bind($result);
		$this->fetch_row();
		return $result;
	}
	
	function last_id()
	{
		if(isset($this->stmt))
		{
			return $this->stmt->insert_id;
		} else {
			return -1;
		}
	}
	
	function error($string, $statement=NULL)
	{
		if($statement)
			print("<hr>SQL: $statement <br>");
		printf("Error: %s || %s<hr>", $string, $this->database->error);
		die();
	}
	
	function __destruct()
	{
		if(isset($this->stmt))
		{
			$this->stmt->close();
		}
		$this->database->close();
	}
}

function make_safe($unSafe)
{
	$safe = strip_tags($unSafe);
	$safe = stripslashes($safe);
	$safe = htmlspecialchars($safe, ENT_NOQUOTES);
	return $safe;
}

if(!isset($db)) {
	global $db;
	$db = new Database();
}
?>