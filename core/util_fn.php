<?php

/*SELECT UNIX_TIMESTAMP(date_entered) FROM sample_table;
UPDATE sample_table SET time_stamp = UNIX_TIMESTAMP(date_entered);*/
// echo 'Current PHP version: ' . phpversion();
/*
// logic - require PHP 5.7 or higher
$fn_db_update_customer = function ($file = "conf.ini"){
	if (!$conf = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');
	$t_qr = $conf["MANTIS_QUERY_LAST_UPDATE"];
	$t_mocha = $conf["MOCHA_TEST"];
	print_r($t_qr);
};*/

class HelperUTILS{
	public static function input_string_valid($str){
        return isset($str) && !empty($str); // && is_scalar($str)
    }
    public static function input_string_escape($inp) {
        if(is_array($inp))  return array_map(__METHOD__, $inp);

        if(input_string_valid($inp)) {
            return str_replace(
                array('\\', "\0", "\n", "\r", "'", '"', "\x1a"),
                array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'),
                $inp
            );
        }
        return $inp;
    }
    public static function load_conf($file = "conf.ini"){
    	// loading configured script
		if (!$conf = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.')
		else
			return $conf;
    }
    public static function mantis_search_result($query_string, $q){
    	$response = [];
    	$response["RESULT"] = db_query_bound( $query_string );
    	$response["COUNT"] = db_num_rows( $result );
    	return $response;
    }
    public static function last_update_time ($query_string){
    	return mysql_query($query_string) or die(mysql_error());
    }
    public static function getCurlData ($http, $q, $status = '&status='){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $http.$q.$status);
		$result = curl_exec($ch);
		curl_close($ch);

		$obj = json_decode($result);

		/*foreach ($obj as $val){
			echo $val->ACCT_DATE. "\t";
		}*/
		return $result;
	}
}

class dbUTILS{

	function __construct($retrieval_date = "03/01/2016") {
		$this->retrieval_date = $retrieval_date;
	}

	function getRetrievalDate(){
		return $this->retrieval_date;
	}
	function setRetrievalDate($val){
		return $this->retrieval_date = $val;
	}

	function execUpdate($p_Curl_result, $p_qr_execute_update){
		$obj = json_decode($p_Curl_result);
		foreach ($obj as $val){
			// TODO: refering to the UTIL class containing input_string_escape function
			// to replace the $this->input_string_escape
			$p_customer_name = HelperUTILS::input_string_escape($val->CUST_NAME);
			$p_timestamp = $val->ACCT_DATE;
			db_query_bound($p_qr_execute_update);
		}
	}

	function updateCustomer(){
		$args = func_get_args();

		if(isset($args[0]) && is_array($args[0]))
			$qr = $args[0];
		$p_unix_update_time = $args[1];
		// TODO: appoint these boolean somewhere
		$g_automatic_updated = true;
		$g_button_clicked = true;
		$g_counter = 0;
		$http_customer = $qr["MANEX_HTTP_REQ_ACCT_DATE"];
		$http_so_wo = $qr["MANEX_HTTP_REQ_SO_WO"];

		$p_max_timestamp = HelperUTILS::last_update_time($qr["MANTIS_QUERY_LAST_UPDATE"]);
		$p_update_time_string = (string) date("m/d/Y", $p_unix_update_time);
		$this->setRetrievalDate($p_update_time_string);

		$g_automatic_updated = $p_unix_update_time - $p_max_timestamp > 3600 && $g_automatic_updated;

		if ($g_automatic_updated){
			// execUpdate
			$this->execUpdate(HelperUTILS::getCurlData($http_customer, $p_unix_update_time), $qr["MANTIS_QUERY_EXECUTE_UPDATE"]);
			return $g_automatic_updated = false;
		} else {
			if ($g_counter<1){
				$g_button_clicked = $p_unix_update_time - $p_max_timestamp > 60 && $g_button_clicked;
				if ($g_button_clicked) {
					// execUpdate
					$this->execUpdate(HelperUTILS::getCurlData($http_customer, $p_unix_update_time), $qr["MANTIS_QUERY_EXECUTE_UPDATE"]);
					$g_counter++;
					return $g_button_clicked = false;
				}
			}
		}
	}

	function sayHello(){
		echo "\n\n -=- MOCHA Testing -=- \n\n";
	}

	function fn_database_update_customer (){
		$args = func_get_args();
		if(isset($args[0]) && is_array($args[0]))
			$qr = $args[0];
		$p_unix_update_time = $args[1],
		$count= $args[2];

		$t_mocha = $qr["MOCHA_TEST"];
		if ($t_mocha){
			$this->sayHello();
			// TODO: write test case
		} else {
			if($count<1){
				$args = [$qr, $p_unix_update_time];
				$this->updateCustomer($args);
			}
		}
	}
}
/*class dbUTILS{
	const CONFIG_FILE_PATH = "conf.ini";
	function __construct() {
		// @todo: change this path to be consistent with outside your webroot
		// $conf = parse_ini_file($this::CONFIG_FILE_PATH, TRUE)
		if (!$conf = parse_ini_file($this::CONFIG_FILE_PATH, TRUE)) throw new exception('Unable to open ' . $this::CONFIG_FILE_PATH . '.');
		// $t_qr = $conf["MANTIS_QUERY_LAST_UPDATE"];
		// $t_mocha = $conf["MOCHA_TEST"];
		$this->fn_db_update_customer = function(){
			// $t_st = $this->$t_qr;
			print_r($conf);
			$t_unix_update_time = strtotime(getDateTime());
			echo $t_unix_update_time. " ". " bravo! \n\n";
			$t_max_timestamp = "qr";
		};
	}
}*/

