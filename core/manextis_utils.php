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
	const CFG_FILE = "cfg/manex_conf.ini";

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
    		if (!$conf = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');
			else
				$config = $conf;
    	} else {
    		foreach ($args as $key => $file) {
    			// loading configured script
				if (!$conf = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');
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
    	$response = [];
		$query = sprintf($query_string, $q);

    	/*$query = $query_string . db_param();
    	// $query = str_replace('%s', db_param(), $query);
    	$query = db_prepare_string($query);
    	$result = db_query_bound( $qr, [$q] );*/

    	$result = db_query_bound( $query );
    	$response["COUNT"] = db_num_rows( $result );
    	if ($response["COUNT"] == 1)
    		$response["RESULT"] = db_result($result);
    	else {
	    	for ($i=0; $i<$response["COUNT"]; $i++ ){
	    		$response["RESULT"][] = db_fetch_array($result);
	    	}
    	}
    	// $response["RESULT"] = mysql_query( $query );
    	return $response;
    }
    /*public static function last_update_time ($query_string){
    	return mysql_query($query_string) or die(mysql_error());
    }*/
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
		$this->retrieval_date = date("m/d/Y", $retrieval_date);
	}

	function getRetrievalDate(){
		return $this->retrieval_date;
	}
	function setRetrievalDate($val){
		return $this->retrieval_date = $val;
	}

	function further_glueing($p_Curl_result, $p_qr_execute_update){
		$obj = json_decode($p_Curl_result);
		return $obj;
		/*foreach ($obj as $val){
			// TODO: refering to the UTIL class containing input_string_escape function
			// to replace the $this->input_string_escape
			$p_customer_name = HelperUTILS::input_string_escape($val->CUST_NAME);
			$p_timestamp = $val->ACCT_DATE;
			db_query_bound($p_qr_execute_update);
		}*/
	}
	function announceTesting($p_query_trigger){
		echo "\n\n -=- MOCHA Testing ". $p_query_trigger ." -=- \n\n";
	}
	function LetippEx(){
		$args = func_get_args();
		$http_request = $args[0];
		$p_query_trigger = $args[1];

		echo HelperUTILS::getCurlData($http_request, $p_query_trigger);
		// $this->execUpdate(HelperUTILS::getCurlData($http_so_wo, $p_query_trigger), $qr["MANTIS_QUERY_EXECUTE_UPDATE"]);
	}
	function fn_database_update (){
		$args = func_get_args();
		$params = $args[0];
		// echo json_encode($args, JSON_PRETTY_PRINT);
		if(isset($params[0]) && is_array($params[0]))
			$qrs = $params[0];

		$count= $params[1];
		$http_request= $params[2];
		$p_query_trigger= $params[3];

		$t_mocha = $qrs["MOCHA_TEST"];
		if ($t_mocha){
			$this->announceTesting($p_query_trigger);
			$this->LetippEx($http_request, $p_query_trigger);
			// TODO: write test case
		} else {
			if($count<1){
				echo "HERE <1";
				$this->LetippEx($http_request, $p_query_trigger);
			}
		}
	}
}