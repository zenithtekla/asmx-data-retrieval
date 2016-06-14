<?php
/**
	* @package DbSkewer
	* @copyright [Env-System] Copyright (C) 2002 - 2014  MantisBT Team - mantisbt-dev@lists.sourceforge.net
	* @copyright [OnTop-Dev] Copyright (C) 2016 ZeTek - https://github.com/zenithtekla
	*/
	/**
	* DbSkewer CoreAPI
*/

class HelperUTILS{
	const CFG_FILE = "cfg/manextis_conf.ini";
	protected static $id;

	public static function input_string_valid($str){
        return isset($str) && !empty($str); // && is_scalar($str)
    }
    public static function input_string_escape($inp) {
        if(is_array($inp))  return array_map(__METHOD__, $inp);

        if(self::input_string_valid($inp)) {
            return str_replace(
                array('\\', "\0", "\n", "\r", "'", '"', "\x1a"),
                array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'),
                $inp
            );
        }
        return $inp;
    }
    public static function string_trim($str){
    	return (self::input_string_valid($str)) ? trim($str) : 'ERROR unable to trim an invalid string!';
    }
    public static function string_no_spaces($str){
    	return (self::input_string_valid($str)) ? preg_replace('/(\v|\s)+/', ' ', $str) : 'ERROR unable to trim spaces of an invalid string!';
    }
    public static function string_trim_strict($str){
    	return (self::input_string_valid($str)) ? self::string_trim(self::string_no_spaces($str)) : 'ERROR unable to razor-trim an invalid string!';
    }
    public static function string_zero_prefix($str){
    	return str_pad($str, 10 - strlen($str), '0', STR_PAD_LEFT);
    }
    /**
	 * Returns an array list of query result
	 * @param array1
	 * @param array2
	 * @return array.response listing elements of array1 (t) that is not part of array2 (x)
	 */
    public static function array_diff_pairs_xt ($arr1, $arr2){
		$result['arr1'] = $arr1;
		$result['arr2'] = $arr2;
		try {
			foreach ($arr1 as $key => $value){
				$arr1_val = self::string_trim($arr1[$key]);
				$arr2_val = self::string_trim($arr2[$key]);
				// if(!$arr2[$key]) throw new Exception( 'eRRoR!');
				if(!$arr2_val) throw new Exception( 'eRRoR!');
				if (strtolower($arr1_val) != strtolower($arr2_val)){
					$result['response']['diff'][$key] = [ $arr1_val, $arr2_val ];
					if($arr1_val == null)
						$result['response']['null'][$key] = [ $arr1_val, $arr2_val ];
				} else $result['response']['same'][$key] = [ $arr1[$key], $arr2[$key] ];
				// 'as is' similarity
			}
		}
		catch (Exception $e){
			$result['response'] = "array_diff_pairs ERROR: " . $e->getMessage();
		}
		finally {
			return $result;
		}
	}
	public static function array_diff_pairs ($arr1, $arr2){
		$result['arr1'] = $arr1;
		$result['arr2'] = $arr2;
		foreach ($arr1 as $key => $value){
			$arr1_val = self::string_trim($arr1[$key]);
			$arr2_val = self::string_trim($arr2[$key]);
			if($arr1_val == null || $arr2_val == null)
				$result['response']['null'][$key] = [ $arr1_val, $arr2_val ];
			if (strtolower($arr1_val) != strtolower($arr2_val)){
				$result['response']['diff'][$key] = [ $arr1_val, $arr2_val ];

			} else $result['response']['same'][$key] = [ $arr1[$key], $arr2[$key] ];
			// 'as is' similarity
		}
			return $result;
	}
	/**
	 * Returns an array list of query result
	 * @param array1
	 * @param array2
	 * @return array listing (diff) elements out of intersection scope.
	 */
	public static function array_diff_merge($arr1, $arr2){
		return array_merge(array_diff($a, $b), array_diff($b, $a));
	}
    public static function load_conf(){
    	$args = func_get_args();
    	$numargs = func_num_args();
    	if (empty($args)) {
    		$file = self::CFG_FILE;
    		if (!$conf = parse_ini_file($file, TRUE)) throw new Exception('Unable to open ' . $file . '.');
			else
				$config = $conf;
    	} else {
    		foreach ($args as $key => $file) {
    			// loading configured script
				if (!$conf = parse_ini_file($file, TRUE)) throw new Exception('Unable to open ' . $file . '.');
				else {
					if ($numargs == 1)
						$config= $conf;
					else
						$config[]= $conf;
				}
    		}
    	}
		return $config;
    }
    public static function mantis_db_query_update(){
	try {
			$response = [];
			$args = func_get_args();
	 		$query_word = ($args[0]==='UPDATE') ? array_shift($args) : 'UPDATE';
			$params = $args;

			$query = call_user_func_array( 'sprintf', $params);
			$result = db_query_bound( $query );
			$response["params"] = $params;
	    	$response["result"] = $result;
	    	$response["query_str"] = self::string_trim_strict($query);
	    	$response["query_word"] = $query_word;
		}
		catch (Exception $e){
			$response["response"] = "mantis_db_query ERROR: " . $e->getMessage();
		}
    	finally {
    		return $response;
    	}
    }
    public static function mantis_db_query_insert(){
	try {
		$response = [];
		$args = func_get_args();
 		$query_word = ($args[0]==='INSERT') ? array_shift($args) : 'INSERT';
		$params = $args;
		if(is_array($args[1])){
			foreach ($args[1] as $key => $value) {
				$query_string = vsprintf($params[0], $value);
				$query[] = $query_string;
				$result[] = db_query_bound( $query_string );
				$response["id"][] = db_insert_id('mantis_wo_so_table_test');
			}
		}
		else {
			$query = call_user_func_array( 'sprintf', $params);
    		$result = db_query_bound( $query );
    		$response["id"] = db_insert_id('mantis_wo_so_table_test');
    	}

    	$response["params"] = $params;
    	$response["result"] = $result;
    	$response["query_str"] = self::string_trim($query);
    	$response["query_word"] = $query_word;
	}
		catch (Exception $e){
			$response["response"] = "mantis_db_query ERROR: " . $e->getMessage();
		}
    	finally {
    		return $response;
    	}
    }
    public static function mantis_db_query_select(){
	try {
		$response = [];
		$args = func_get_args();
		$query_word = ($args[0]==='SELECT') ? array_shift($args) : 'SELECT';
		$params = $args;
		$query = call_user_func_array( 'sprintf', $params[0]);
    	/*$query = $query_string . db_param();
    	// $query = str_replace('%s', db_param(), $query);
    	$query = db_prepare_string($query);
    	$result = db_query_bound( $qr, [$q] );*/

    	$result = db_query_bound( $query );
    	$response["count"] = db_num_rows( $result );
    	if ($response["count"]>0)
    	for ($i=0; $i<$response["count"]; $i++ ){
    		$response["response"][] = db_fetch_array($result);
    	}

    	$response["query_str"] = self::string_trim_strict($query);
    	$response["query_word"] = $query_word;
	}
		catch (Exception $e){
			$response["response"] = "mantis_db_query ERROR: " . $e->getMessage();
		}
    	// $response["response"] = mysql_query( $query );
    	finally {
    		return $response;
    	}
    }
    /**
	 * Returns an array list of query result
	 * @param [0] = $query_string
	 * @param [1], [2].. = array of parameters to pass in a query function be invoked through swtich
	 * @return array
	 */
    public static function mantis_db_query(){
	try {
		$params = func_get_args();
		$query_string = self::string_trim($params[0]);
		if (strlen($query_string) > 12)
			$query_word = substr($query_string, 0, 12);
		switch (true) {
			case stristr($query_word, 'INSERT'):
				throw new Exception('Exception handler for INSERT query not yet included.');
				break;
			case stristr($query_word, 'SELECT'):
				$response = self::mantis_db_query_select('SELECT', $params);
				break;
			case stristr($query_word, 'UPDATE'):
				break;
			case stristr($query_word, 'DELETE'):
				break;
			default:
				throw new Exception('Exception handler for query not yet included.');
				break;
		}
	}
		catch (Exception $e){
			$response["response"] = "mantis_db_query ERROR: " . $e->getMessage();
		}
    	finally {
    		return $response;
    	}
    }
    public static function getCurlData ($http, $q, $status = '&status='){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $http.$q.$status);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}

class SkewChess{
	function __construct($retrieval_date ="") {
		// $this->retrieval_date = strtotime(date("m/d/Y", $retrieval_date));
		$this->retrieval_date = $retrieval_date;
	}

	function fn_process_SkewedData($p_Curl_result, $p_qr_execute_update){
		$obj = json_decode($p_Curl_result);
		return $obj;
		/*foreach ($obj as $val){
			$p_customer_name = HelperUTILS::input_string_escape($val->CUST_NAME);
			$p_timestamp = $val->ACCT_DATE;
			db_query_bound($p_qr_execute_update);
		}*/
	}
	function getSkewedData(){
		$args = func_get_args();
		$http_request = $args[0];
		$p_query_trigger = $args[1];
		$result = HelperUTILS::getCurlData($http_request, $p_query_trigger);
		return $result;
	}
	function fn_skew_manexDb (){
		$args = func_get_args();
		$params = $args[0];
		if(isset($params[0]) && is_array($params[0]))
			$qrs = $params[0];

		$count= $params[1];
		$http_request= $params[2];
		$p_query_trigger= $params[3];

		$t_mocha = $qrs["MOCHA_TEST"];
		if ($t_mocha){
			// MOCHA Testing in progress
			if($count<1)
				return $this->getSkewedData($http_request, $p_query_trigger);
		} else {
			if($count<1){
				return $this->getSkewedData($http_request, $p_query_trigger);
			}
		}
	}
}