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
    public static function mantis_db_query($query_string, $q){
	try {
		if (strpos($query_string, 'SELECT') === false) throw new Exception('Exception handler for query other than SELECT');

		$response = [];
		$query = call_user_func_array( 'sprintf', func_get_args());
		// $query = (is_array($q)) ? vsprintf($query_string, $q) : sprintf($query_string, $q);
    	/*$query = $query_string . db_param();
    	// $query = str_replace('%s', db_param(), $query);
    	$query = db_prepare_string($query);
    	$result = db_query_bound( $qr, [$q] );*/

    	$result = db_query_bound( $query );
    	$response["count"] = db_num_rows( $result );
    	if ($response["count"] == 1)
    		$response["response"] = db_result($result);
    	else {
	    	for ($i=0; $i<$response["count"]; $i++ ){
	    		$response["response"][] = db_fetch_array($result);
	    	}
    	}
    	$response["query_str"] = $query;
	}
		catch (Exception $e){
			$response["response"] = "mantis_db_query ERROR: " . $e->getMessage();
		}
    	// $response["response"] = mysql_query( $query );
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
	function announceTesting($p_query_trigger){
		return " -=- MOCHA Testing ". $p_query_trigger ." -=- ";
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
			$this->announceTesting($p_query_trigger);
			if($count<1)
				return $this->getSkewedData($http_request, $p_query_trigger);
		} else {
			if($count<1){
				return $this->getSkewedData($http_request, $p_query_trigger);
			}
		}
	}
}