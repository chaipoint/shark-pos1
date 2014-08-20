<?php

class Database{

	/**
	 * Store the single instance of Database
	 * @var Object
	 */
	private static $m_pInstance;

	/**
	 * Private constructor to limit object instantiation to within the class
	 * @return none
	 */
	public function __construct()
	{
		global $sql_host,$sql_user,$sql_password,$sql_db;

		self::$m_pInstance = mysql_connect($sql_host, $sql_user, $sql_password, false, MYSQL_CLIENT_INTERACTIVE);

		mysql_select_db($sql_db) || die("Could not connect to SQL db");
	}

	/**
	 * Getter method for creating/returning the single instance of this class
	 * @return Instance
	 */
	public static function getInstance()
	{
		if (!self::$m_pInstance)
		{
			self::$m_pInstance = new Database();
		}

		return self::$m_pInstance;
	}

	/**
	 * returns the mysql query resource
	 * @param String $query
	 * @return mysql_resource
	 */
	public function db_query($query) {
		$result =  mysql_query($query);
		if($result){
			return $result;
		}
		else{
			die(self::db_error($query));
		}
	}

	/**
	 * returns the mysql result data for given offset
	 * @param string $result
	 * @param string $offset
	 * @return string
	 */
	private function db_result($result, $offset) {
		return mysql_result($result, $offset);
	}

	/**
	 * fetch only one row at a time
	 * @param $result
	 * @return array
	 */
	private function db_fetch_row($result) {
		return mysql_fetch_row($result);
	}

	/**
	 * fetch the all record from db and returns array
	 * @param $result
	 * @param $flag
	 * @return array
	 */
	private function db_fetch_array($result, $flag=MYSQL_ASSOC) {
		return mysql_fetch_array($result, $flag);
	}

	/**
	 * free the mysql resource from memory
	 * @param mysql_resource $result
	 * @return 1
	 */
	private function db_free_result($result) {
		@mysql_free_result($result);
	}

	/**
	 * return the number of rows for executed query
	 * @param mysql_resource $result
	 * @return Integer
	 */
	private function db_num_rows($result) {
		return @mysql_num_rows($result);
	}

	/**
	 * return the last auto generated id
	 * @return Integer
	 */
	public function db_insert_id() {
		return mysql_insert_id();
	}

	/**
	 * retuns the number of affected rows by delete, update commands
	 * @return Integer
	 */
	public function db_affected_rows() {
		return mysql_affected_rows();
	}

	/**
	 * Prepair the sql error
	 * @param string $query
	 * @return none
	 */
	public function db_error($query) {
		$mysql_error = mysql_errno()." : ".mysql_error();
		$msg  = "Site        : ".$_SERVER['HTTP_HOST']."<br />";
		$msg .= "Remote IP   : ".$_SERVER['REMOTE_ADDR']."<br />";
		$msg .= "SQL query   : $query<br />";
		$msg .= "Error code  : ".mysql_errno()."<br/>";
		$msg .= "Description : ".mysql_error();

		self::db_error_generic($query, $mysql_error, $msg);

		return true;
	}

	/**
	 * Print the error messge to user
	 * @param string $query
	 * @param string $query_error
	 * @param string $msg
	 * @return string
	 */
	private function db_error_generic($query, $query_error, $msg) {
		global $debug_mode;

		if ($debug_mode == 1) {
			echo "<b><font COLOR=DARKRED>INVALID SQL: </font></b>".htmlspecialchars($query_error)."<br />";
			echo "<b><font COLOR=DARKRED>SQL QUERY FAILURE:</font></b>".($msg)."<br />";
			flush();
		}
	}

	/**
	 *
	 * @param string $query
	 * @param array $params
	 * @return multitype:string number Ambigous <number, multitype:> |multitype:string number Ambigous <number, multitype:> |string
	 */
	public function db_prepare_query($query, $params) {
		static $prepared = array();

		if (!empty($prepared[$query])) {
			$info = $prepared[$query];
			$tokens = $info['tokens'];
		}
		else {
			$tokens = preg_split('/((?<!\\\)\?)/S', $query, -1, PREG_SPLIT_DELIM_CAPTURE);

			$count = 0;
			foreach ($tokens as $k=>$v) if ($v === '?') $count ++;

			$info = array (
			'tokens' => $tokens,
			'param_count' => $count
			);
			$prepared[$query] = $info;
		}

		if (count($params) != $info['param_count']) {
			return array (
			'info' => 'mismatch',
			'expected' => $info['param_count'],
			'actual' => count($params));
		}

		$pos = 0;
		foreach ($tokens as $k=>$val) {
			if ($val !== '?') continue;

			if (!isset($params[$pos])) {
				return array (
				'info' => 'missing',
				'param' => $pos,
				'expected' => $info['param_count'],
				'actual' => count($params));
			}

			$val = $params[$pos];
			if (is_array($val)) {
				$val = self::func_array_map('addslashes', $val);
				$val = implode("','", $val);
			}
			else {
				$val = addslashes($val);
			}

			$tokens[$k] = "'" . $val . "'";
			$pos ++;
		}

		return implode('', $tokens);
	}


	/**
	 * New DB API: Executing parameterized queries
	 * Example1:
	 * $query = "SELECT * FROM table WHERE field1=? AND field2=? AND field3='\\?'"
	 * $params = array (val1, val2)
	 * query to execute:
	 * "SELECT * FROM table WHERE field1='val1' AND field2='val2' AND field3='\\?'"

	 * Example2:
	 * $query = "SELECT * FROM table WHERE field1=? AND field2 IN (?)"
	 * $params = array (val1, array(val2,val3))
	 * query to execute:
	 * "SELECT * FROM table WHERE field1='val1' AND field2 IN ('val2','val3')"

	 * Warning:
	 * 1) all parameters must not be escaped with addslashes()
	 * 2) non-parameter symbols '?' must be escaped with a '\'

	 * @param string $query
	 * @param array $params
	 * @return array
	 */
	public function db_exec($query, $params=array()) {

		if (!is_array($params))
		$params = array ($params);

		$prepared = self::db_prepare_query($query, $params);

		if (!is_array($prepared)) {
			return self::db_query($prepared);
		}

		$error = "Query preparation failed";
		switch ($prepared['info']) {
			case 'mismatch':
				$error .= ": parameters mismatch (passed $prepared[actual], expected $prepared[expected])";
				break;
			case 'missing':
				$error .= ": parameter $prepared[param] is missing";
				break;
		}

		$msg .= "SQL query   : $query\n";
		$msg .= "Description : ".$error;

		self::db_error_generic($query, $error, $msg);

		return false;
	}



	/**
	 * Execute mysql query adn store result into associative array with column names as keys...
	 * @param string $query
	 * @return array
	 */
	public function func_query($query) {
		$result = false;
		if ($p_result  = self::db_query($query)) {
			while($arr = self::db_fetch_array($p_result))
			$result[]=$arr;
			self::db_free_result($p_result);
		}

		return $result;
	}

	/**
	 * Execute mysql query and store result into associative array with column names as keys and then return first element of this array
	 * @param $query
	 * @return array
	 */
	public function func_query_first($query) {
		if ($p_result = self::db_query($query)) {
			$result   = self::db_fetch_array($p_result);
			self::db_free_result($p_result);
		}

		return is_array($result)? $result : array();
	}


	/**
	 * Execute mysql query and store result into associative array with  column names as keys and then return first cell of first element of this array
	 * @param $query
	 * @return string , If array is empty return false.
	 */
	public function func_query_first_cell($query) {
		if ($p_result = self::db_query($query)) {
			$result   = self::db_fetch_row($p_result);
			self::db_free_result($p_result);
		}
		return is_array($result)?$result[0]:false;
	}

	/**
	 * Insert array data to table
	 * @param $tbl
	 * @param $arr
	 * @param $is_replace
	 * @return unknown_type
	 */
	public function func_array2insert ($tbl, $arr, $is_replace = false) {
		if(self::db_query(($is_replace?"REPLACE":"INSERT")." INTO $tbl (" . implode(", ", array_keys($arr)) . ") VALUES ('" . implode("', '", $arr) . "')"))
		{
			return self::db_insert_id();
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update array data to table + where statament
	 * @param string $tbl
	 * @param array $arr
	 * @param string $where
	 * @return integer
	 */
	public function func_array2update ($tbl, $arr, $where = '') {
		$r = '';
		foreach($arr as $k => $v) {
			$r .= ($r ? ", " : "") . $k . "='" . $v . "'";
		}
		return self::db_query("UPDATE $tbl SET $r" . ($where ? " WHERE " . $where : ""));
	}

	/**
	 *
	 * @param string $func
	 * @param string $var
	 * @return string
	 */
	public function func_array_map($func, $var) {
		if (!is_array($var)) return $var;

		foreach($var as $k=>$v)
		$var[$k] = call_user_func($func,$v);

		return $var;
	}

	/**
	 *
	 * @param string $query
	 * @return int
	 */
	public function rowcount($query){
		$result = self::db_query($query);
		return self::db_num_rows($result);
	}
}
?>