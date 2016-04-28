<?php

/*SELECT UNIX_TIMESTAMP(date_entered) FROM sample_table;
UPDATE sample_table SET time_stamp = UNIX_TIMESTAMP(date_entered);*/
// echo 'Current PHP version: ' . phpversion();
/*
// logic - require PHP 5.7 or higher
$fn_db_update_customer = function ($file = "conf.ini"){
	if (!$conf = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');
	$t_qr = $conf["QUERY_LAST_UPDATE"];
	$t_mocha = $conf["MOCHA_TEST"];
	print_r($t_qr);
};*/

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

	// Universal UTILS (UTILS = Utilities) ///
	function input_string_valid($str){
        return isset($str) && !empty($str);
    }
    function input_string_escape($inp) {
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
    //////////////////////////////////////////

	function getCurlData (){
		$date = $this->getRetrievalDate();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, 'http://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate?Acct_Date='.$date.'&status=');
		$result = curl_exec($ch);
		curl_close($ch);

		$obj = json_decode($result);

		/*foreach ($obj as $val){
			echo $val->ACCT_DATE. "\t";
		}*/
		return $result;
	}

	function execUpdate($p_Curl_result, $p_qr_execude_update){
		$obj = json_decode($p_Curl_result);
		foreach ($obj as $val){
			// TODO: refering to the UTIL class containing input_string_escape function
			// to replace the $this->input_string_escape
			$p_customer_name = $this->input_string_escape($val->CUST_NAME);
			$p_timestamp = $val->ACCT_DATE;
			db_query_bound($p_qr_execude_update);
		}
	}

	function updateCustomer($p_unix_update_time, $query, $p_qr_execude_update){
		// TODO: appoint these boolean somewhere
		$g_automatic_updated = true;
		$g_button_clicked = true;
		$g_counter = 0;
		$p_max_timestamp = mysql_query($query) or die(mysql_error());
		$p_update_time_string = (string) date("m/d/Y", $p_unix_update_time);
		$this->setRetrievalDate($p_update_time_string);

		$g_automatic_updated = $p_unix_update_time - $p_max_timestamp > 3600 && $g_automatic_updated;

		if ($g_automatic_updated){
			// execUpdate
			$this->execUpdate($this->getCurlData(), $p_qr_execude_update);
			return $g_automatic_updated = false;
		} else {
			if ($g_counter<1){
				$g_button_clicked = $p_unix_update_time - $p_max_timestamp > 60 && $g_button_clicked;
				if ($g_button_clicked) {
					// execUpdate
					$this->execUpdate($this->getCurlData(), $p_qr_execude_update);
					$g_counter++;
					return $g_button_clicked = false;
				}
			}
		}
	}

	function sayHello(){
		echo "\n\n -=-MOCHA Testing -=- \n\n";
	}

	function fn_database_update_customer ($t_unix_update_time, $file = "conf.ini"){
		// loading configured script
		if (!$conf = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');
		$t_qr = $conf["QUERY_LAST_UPDATE"];
		$t_mocha = $conf["MOCHA_TEST"];
		$t_qr_execude_update = $conf["QUERY_EXECUTE_UPDATE"];
		if ($t_mocha){
			$this->sayHello();
			// TODO: write test case
		} else {
			$this->updateCustomer($t_unix_update_time, $t_qr, $t_qr_execude_update);
		}
	}
}
/*class dbUTILS{
	const CONFIG_FILE_PATH = "conf.ini";
	function __construct() {
		// @todo: change this path to be consistent with outside your webroot
		// $conf = parse_ini_file($this::CONFIG_FILE_PATH, TRUE)
		if (!$conf = parse_ini_file($this::CONFIG_FILE_PATH, TRUE)) throw new exception('Unable to open ' . $this::CONFIG_FILE_PATH . '.');
		// $t_qr = $conf["QUERY_LAST_UPDATE"];
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

