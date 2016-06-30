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
	const CFG_FILE = 'cfg/manextis_conf.ini';
	protected static $id;
	public static function input_string_valid($str){
        return is_string($str) && isset($str) && !empty($str); // && is_scalar($str)
    }
    public static function input_string_escape($inp) {
    	// escape inputs for form, query and security
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
    	// trim from the beginning and end of the string
    	return (self::input_string_valid($str)) ? trim($str) : 'ERROR unable to trim an invalid string!';
    }
    public static function string_no_spaces($str){
    	return (self::input_string_valid($str)) ? preg_replace('/(\v|\s)+/', ' ', $str) : 'ERROR unable to trim spaces of an invalid string!';
    }
    public static function string_trim_strict($str){
    	// trim spaces from anywhere
    	return (self::input_string_valid($str)) ? self::string_trim(self::string_no_spaces($str)) : 'ERROR unable to razor-trim an invalid string!';
    }
    public static function string_zero_prefix($str){
    	return (is_numeric($str) && $str > 0 && $str == round($str, 0)) ? str_pad($str, 10, '0', STR_PAD_LEFT) : $str;
    }
    public static function query_trigger_handler($query_trigger){
    	return self::string_zero_prefix(self::string_trim($query_trigger));
    }
    /**
	 * Returns an array list of query result
	 * @param array1
	 * @param array2
	 * @return array.response listing elements of array1 (x) that is not part of array2 (t)
	 * Limitation: array1 has m elements and array2 has n elements
	 * with m > n , array1: haystack, array2: needles
	 * tx: ManTis-ManeX databases
	 * this method is specific to compare T query result with X query result
	 * and gives response indicating the diff, null, and same
	 * Thus, pass array2 as a haystack for this method to work effectively
	 */
    public static function array_diff_pairs_xt ($arr1, $arr2){
		try {
			foreach ($arr1 as $key => $value){
				$arr1_val = (self::input_string_valid($arr1[$key])) ? strtolower(self::string_trim($arr1[$key])) : $arr1[$key];
				$arr2_val = (self::input_string_valid($arr2[$key])) ? strtolower(self::string_trim($arr2[$key])) : $arr2[$key];
				$result['arr1'][$key] = $arr1_val;
				$result['arr2'][$key] = $arr2_val;
				// verify both value and value type
				if ($arr2_val !== $arr1_val){
					$result['response']['all_diff'][$key] = [ $arr1[$key], $arr2[$key] ];
					if($arr2_val == null)
						$result['response']['null'][$key] = [ $arr1[$key], $arr2[$key] ];
					else
						$result['response']['diff'][$key] = [ $arr1[$key], $arr2[$key] ];
				} else  $result['response']['same'][$key] = [ $arr1[$key], $arr2[$key] ];
				// 'as is' similarity
			}
		}
		catch (Exception $e){
			$result['response'] = 'array_diff_pairs_xt ERROR: ' . $e->getMessage();
		}
		finally {
			return $result;
		}
	}
	public static function array_diff_pairs ($arr1, $arr2){
		foreach ($arr2 as $key => $value){
			$arr2_val = (is_string($arr2[$key])) ? strtolower(self::string_trim($arr2[$key])) : $arr2[$key];
			$arr1_val = (is_string($arr1[$key])) ? strtolower(self::string_trim($arr1[$key])) : $arr1[$key];
			$result['arr2'][$key] = $arr2_val;
			$result['arr1'][$key] = $arr1_val;
			if($arr2_val == null || $arr1_val == null)
				$result['response']['null'][$key] = [ $arr2[$key], $arr1[$key] ];
			if ($arr2_val != $arr1_val){
				$result['response']['diff'][$key] = [ $arr2[$key], $arr1[$key] ];
			} else $result['response']['same'][$key] = [ $arr2[$key], $arr1[$key] ];
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
    	// verify if array is empty
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

    // this method will perform update even if entry does not exist; use w/mantis_db_query to perform check on result count of a SELECT query if necessary, see '06 - query_text exists' of xt_sync_update() method for example
    public static function mantis_db_query_update(){
	try {
			$response = [];
			$args = func_get_args();
			$response['args'] = $args;
			if (count($args) === 1){
				if ( is_string($args[0])) $query = $args;
				if ( is_array($args[0]) ) $query = $args[0];
			} else {
				$response['update_query'] = self::mantis_db_query_build($args);
				$query = $response['update_query']['query_string'];
			}

			foreach ($query as $qr) {
				if (!is_array($qr))
				$response['response']['bound'] = db_query_bound( $qr );
			}
			$response['response']['text'] = 'Query update successfully executed.';
		}
		catch (Exception $e){
			$response['response'] = 'mantis_db_query ERROR: ' . $e->getMessage();
		}
    	finally {
    		return $response;
    	}
    }

    // REQUIRE strict; on params, so start off with query_build before calling this method.
    public static function mantis_db_query_insert(){
	try {
		$response = [];
		$args = func_get_args();
		$response = self::mantis_db_query_build($args);
		$query_string = $response['query_string'];
		$response['response'] = self::mantis_db_invoke_insert($query_string, $response['table_of_insert']);
	}
		catch (Exception $e){
			$response->response = 'mantis_db_insert ERROR: ' . $e->getMessage();
		}
    	finally {
    		return $response;
    	}
    }

    // REQUIRE strict; on params, so start off with query_build before calling this method.
    public static function mantis_db_invoke_insert($query, $table){
	try {
		if (is_array($query)){
			foreach ($query as $qr) {
				db_query_bound( $qr);
				$response['id'][] = db_insert_id( $table );
			}
		} else {
			db_query_bound( $query );
			$response['id'][] = db_insert_id( $table );
		}
	}
		catch (Exception $e){
			$response->response = 'mantis_db_invoke_insert ERROR: ' . $e->getMessage();
		}
    	finally {
    		return $response;
    	}
    }
    public static function mantis_db_query_select(){
	try {
		$response = [];
		$args = func_get_args();
		// $response['args'] = $args;
		$result_buffer_check = false;

		if (count($args) === 1){
			if ( is_string($args[0])) $query = $args;
			if ( is_array($args[0]) ) $query = $args[0];
		} else {
			$response['select_query'] = self::mantis_db_query_build($args);
			$query = $response['select_query']['query_string'];
		}
		$response['query_string'] = $query;

		foreach ($query as $qr) {
			if (!is_array($qr)){
				$result[] = db_query_bound( $qr );
				$result_buffer_check = true;
			}
		}
		if (is_array($result)){
			$result = array_unique($result);
			if (count($result) > 1) throw new Exception('More than one SELECT query executed');
			if (count($result) == 0) throw new Exception('No SELECT query performed');
		}
		$result = ($result_buffer_check) ? $result[0] : $result;
		$response['count'] = db_num_rows( $result );
		if ($response['count']>0)
    	for ($i=0; $i<$response['count']; $i++ ){
    		$response['response'][] = db_fetch_array($result);
    	}
	}
		catch (Exception $e){
			$response['response'] = 'mantis_db_query_select ERROR: ' . $e->getMessage();
		}
    	// $response["response"] = mysql_query( $query );
    	finally {
    		return $response;
    	}
    }

    private static function is_sql_word($string){
    	$result = new stdClass();
    	switch (true) {
    		case preg_match("/SELECT/", $string, $match):
    			$result->bool = true;
    			$result->word = $match;
    			break;
    		case preg_match("/INSERT/", $string, $match):
    			$result->bool = true;
    			$result->word = $match;
    			break;
    		case preg_match("/UPDATE/", $string, $match):
    			$result->bool = true;
    			$result->word = $match;
    			break;
    		case preg_match("/DELETE/", $string, $match):
    			$result->bool = true;
    			$result->word = $match;
    			break;
    		default:
    			$result->bool = false;
    			break;
    	}
    	return $result;
    }

    public static function mantis_db_query_build(){
		try {
			$response = [];
			$args = func_get_args();
			if (count($args)==1) $args = $args[0];

			$query_string = self::string_trim($args[0]);
			$query_word = substr($query_string, 0, 6);
			$o_sql_word = self::is_sql_word($query_word);
			if ($o_sql_word->bool) {
				if (strlen($query_string) < 7) array_shift($args);
				$response['query_word'] = $o_sql_word->word;
			}

			$params = $args;
			$count = 0;
			foreach ($params as $param) {
				// not going to handle many arrays here
				if (is_array($param)){
 					$t_query[] = vsprintf($params[0], $param);
 					$t_table_of_insert = $param[0];
					$count++;
				}
			}

			$response['params'] = $params;
			if ($count < 1)
				$response['query_string'] = call_user_func_array( 'sprintf', $params);
			else{
				$response['query_string'] = array_unique($t_query);
				$response['table_of_insert'] = $t_table_of_insert;
			}
		}
		catch (Exception $e){
			$response->response = 'mantis_db_query_build ERROR: ' . $e->getMessage();
		}
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
		$response = [];
		$args = func_get_args();
		$response = self::mantis_db_query_build($args);
		$query_word = $response['query_word'];
		$query_word = (is_array($query_word)) ? $query_word[0] : $query_word;
		$query_string = $response['query_string'];
		switch (true) {
    		case ($query_word ==='SELECT'):
    			$response['response'] = self::mantis_db_query_select($query_string );
    			break;
    		case ($query_word ==='INSERT'):
    			// invoke insertion
    			$response['response'] = self::mantis_db_invoke_insert($query_string, $response['table_of_insert']);
    			break;
    		case ($query_word ==='UPDATE'):
    			$response['response'] = self::mantis_db_query_update($query_string);
    			break;
    		case ($query_word ==='DELETE'):
    			$response['type'] = gettype($query_string);
    			break;
    		default:
    			#
    			break;
    	}
	}
		catch (Exception $e){
			$response['response'] = 'mantis_db_query ERROR: ' . $e->getMessage();
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
/**
* @require class HelperUTILS
*/
class SkewChess{
	// initialization
	function __construct($query_trigger, $t_creator_id = '') {
		$this->query_trigger = HelperUTILS::query_trigger_handler($query_trigger);
		$this->creator_id = HelperUTILS::input_string_escape($t_creator_id);
	}
	function getQueryTrigger(){
		return $this->query_trigger;
	}
	function getCreatorId(){
		return $this->creator_id;
	}
	function setQueryTrigger($val){
		$this->query_trigger = HelperUTILS::query_trigger_handler($val);
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
	/**
	 * Returns an array list of query result
	 * @param specific CONSTANT STRING $http_request from ini file to be executed.
 	 * NO count as MantisDb not evaluated.
	 * @return json string
	 */
	function manex_db_query($http_request){
		$p_query_trigger = $this->getQueryTrigger();
		// on server machine, getQueryPrefixedTrigger() might be used instead.
		$result = HelperUTILS::getCurlData($http_request, $p_query_trigger);
		return $result;
	}
	/**
	 * Returns an array list of query result
	 * @param specific CONSTANT STRING $http_request from ini file to be executed.
 	 * @param $count number of result from a previous MantisDb query
	 * @return json string
	 */
	function fn_skew_manexDb (){
		$params = func_get_arg(0);
		$http_request= $params[0];
		$count= $params[1];
		if($count<1)
			return $this->manex_db_query($http_request);
	}
	function xt_compare($arr1, $arr2){
		return HelperUTILS::array_diff_pairs_xt($arr1, $arr2);
	}

	/**
	* Build update query
	*/
	function update_a_record($p_update, $p_query, $p_table, $p_where){
		$p_set_at = implode(', ', array_keys($p_update));
		$p_query = str_replace('?', $p_set_at, $p_query);
		$params = array_values($p_update);
		array_unshift($params, $p_table);
		array_push($params, $p_where);
		return HelperUTILS::mantis_db_query_build($p_query, $params);
	}

	/**
	* Build and Invoke Insertion of new data set
	*/
	function xt_sync_insert_all(
		$p_insert_customer_table,
		$p_insert_assembly_table,
		$p_insert_wo_so_table, $source, $testing= false){
		$t_status = 1; // insert and set active
		$params = [
			$source['customer_table'],
			$source['CUST_NAME'],
			$source['CUST_PO_NO'],
			$t_status,
			$source['ACCT_DATE'],
			$source['TIME_STAMP']
		];
		$t_query_string = HelperUTILS::mantis_db_query_build($p_insert_customer_table, $params);
		// return $t_qr;
		$t_id = ($source['Mocha']) ? 29 : HelperUTILS::mantis_db_invoke_insert($t_query_string, $source['customer_table'])->id;
		$result[] = $t_query_string;

		// perform next query
		$params = [
			$source['assembly_table'],
			$t_id,
			$source['ASSY_NO'],
			$source['REVISION'],
			$source['UNIQ_KEY'],
		];
		$t_query_string = HelperUTILS::mantis_db_query_build($p_insert_assembly_table, $params);
		$result[] = $t_query_string;

		// perform last query
		$params = [
			$source['wo_so_table'],
			$source['WO_NO'],
			$source['SO_NO'],
			$source['QTY'],
			$source['DUE'],
			$source['UNIQ_KEY'],
		];
		$t_query_string = HelperUTILS::mantis_db_query_build($p_insert_wo_so_table, $params);
		$result[] = $t_query_string;

		return $result;
	}
	/**
	* Returns an array list of query result
	* @param X_query_str
	* @param T_query_str
	* @return array
	*/
	function xt_sync($X_query_str, $T_query_str){
		$X_response = $this->manex_db_query($X_query_str);
		$X_res_arr = json_decode($X_response, true);
		if ($X_response){
			$result['Xquery']['count'] = count($X_res_arr);
			$result['Xquery']['response'] = $X_response;
			$result['Xquery']['query_str'] = $X_query_str;
		}
		/* ---
				END Xresponse
				BEGIN Tresponse
		   ---
		*/
		$T_response = HelperUTILS::mantis_db_query($T_query_str, $this->getQueryTrigger());
		$result['Tquery'] = $T_response;

		$result['XTcompare'] = $this->xt_compare($X_res_arr[0], $T_response['response'][0]);
		return $result;
	}

	/**
	* Returns an array list of query result
	* @param X_query_str
	* @param T_query_str
	* @param o_Mocha carries over pre-defined settings
	* @return array
	*/

	function xt_sync_update($X_query_str, $T_query_str, $o_Mocha = null){
		try {
			$X_response = $this->manex_db_query($X_query_str);
			if ($X_response){
				$X_res_arr = json_decode($X_response, true);
				$X_res_count = count($X_res_arr);
				// override result count
				$result['Xquery']['count'] = ($o_Mocha->testing && !empty($o_Mocha->x_res_count)) ? $o_Mocha->x_res_count : $X_res_count;

				if ($result['Xquery']['count'] < 1) throw new Exception('02.1 - X_Query result has NO matching entry');
				if ($result['Xquery']['count'] > 1) throw new Exception('03.1 - X_Query result contains more than one entry');

				$result['Xquery']['response'] = $X_response;
				$result['Xquery']['query_str'] = $X_query_str;

				/* ---
				END X_response
				BEGIN T_response, load table names from conf.ini
				   ---
				*/
				// get timestamp
				$t_timestamp 				= $o_Mocha->timestamp;
				// get table names
				$q_wo_so_table    			= $o_Mocha->wo_so_table;
				$q_assembly_table 			= $o_Mocha->assembly_table;
				$q_customer_table 			= $o_Mocha->customer_table;

				$T_response = HelperUTILS::mantis_db_query($T_query_str, $q_wo_so_table, $this->getQueryTrigger());
				$result['Tquery'] = $T_response;

				/* ---
				END T_response
				DONE X & T retrieval
				   ---
				*/

				//  .sync.response.response.
				// fetch result to ensure front-end display; otherwise, fetch later and use front-end display as a method to verify the entire process.
				$result['response']['response'] = $X_response;

				/* --- OVERRIDE result count --- */
				if ($o_Mocha->testing && !empty($o_Mocha->t_res_count))
				$result['Tquery']['count'] = $o_Mocha->t_res_count;

				/* --- CONDITION check --- */
				if ($result['Tquery']['count'] > 1) throw new Exception('03.2 - T_Query result contains more than one entry');

				/* --- OVERRIDE res_array
						if testing is enabled--- */
 				//  Limitation: only grab first element of the result array.
				$T_res_array = ($o_Mocha->testing && !empty($o_Mocha->t_res_arr)) ? $o_Mocha->t_res_arr[0] : $T_response['response'][0];
				$X_res_array = ($o_Mocha->testing && !empty($o_Mocha->x_res_arr)) ? $o_Mocha->x_res_arr[0] : $X_res_arr[0];

				/* --- CONDITION check --- */
				if (empty($X_res_arr[0])) throw new Exception('02.2 - X_Query result has NO matching entry OR connection to Manex drops!');

				/* ---
				INSERT_ALL if NO query result from manTis
				   ---
				*/
				// if ($result['Tquery']['count'] < 1) throw new Exception('04 - Ready to insert ALL mantis_db_invoke_insert()');
				if ($result['Tquery']['count'] < 1){
					$X_res_array['TIME_STAMP'] 		= $t_timestamp;
					$X_res_array['Mocha'] 			= $o_Mocha->testing;
					$X_res_array['wo_so_table'] 	= $q_wo_so_table;
					$X_res_array['assembly_table'] 	= $q_assembly_table;
					$X_res_array['customer_table'] 	= $q_customer_table;

					$q_insert_wo_so_table    	= $o_Mocha->insert_wo_so_table;
					$q_insert_assembly_table 	= $o_Mocha->insert_assembly_table;
					$q_insert_customer_table 	= $o_Mocha->insert_customer_table;
					$result['pendingInsert'] = $this->xt_sync_insert_all($q_insert_customer_table, $q_insert_assembly_table, $q_insert_wo_so_table, $X_res_array);
					// return from this method after insert_all, the rest code should be ignored.
					// invoke insertion
					foreach ($result['pendingInsert'] as $query) {
						$result['insertAll'][] = HelperUTILS::mantis_db_invoke_insert($query['query_string'], $query['table_of_insert']);
					}
					return $result;
				}

				/* ---
				SYNC.UPDATE ManTis.entry
				   ---
				*/

				$result['XTcompare'] = $this->xt_compare($X_res_array, $T_res_array);

				if (!is_array($result['XTcompare']['response'])) throw new Exception ('05 - XT_Compare failed, review query results or testing inputs');

				$XT_all_diff = $result['XTcompare']['response']['all_diff'];
				$XT_diff = $result['XTcompare']['response']['diff'];
				$XT_same = $result['XTcompare']['response']['same'];
				$XT_null = $result['XTcompare']['response']['null'];
				/* this is again where ugly PHP is madness where simply
				o.xt_compare.res.diff is implemented in other language.
				*/

				// set 3 key values;
				$t_uniq_key 	= $X_res_array['UNIQ_KEY']; // using Manex.UNIQ_KEY  on Mantis too.
				$t_customer_id 	= $T_res_array['CUST_ID'];
				$t_wo 			= $X_res_array['WO_NO']; // = $this->getQueryTrigger() = $T_res_array['WO_NO']

				// instead of going and updating everything using 3 queries QUERY_UPDATE_WO_TABLE, QUERY_UPDATE_ASSEMBLY_TABLE, and QUERY_UPDATE_CUSTOMER_TABLE which disregard performance and does all update anyway, the righteous approach should be to the following route of query string build.
				$t_update_for = [];
				foreach ($XT_all_diff as $key => $value) {
					$t_key = $key;
					switch (true) {
						case preg_match("/SO_NO/", $key, $match):
							$t_set_at = "sono='%d'";
							$t_update_for[$q_wo_so_table][$t_set_at] = $value[0];
							break;
						case preg_match("/QTY/", $key, $match):
							$t_set_at = "quantity='%d'";
							$t_update_for[$q_wo_so_table][$t_set_at] = $value[0];
							break;
						case preg_match("/DUE_DATE/", $key, $match):
							$t_set_at = "due='%d'";
							$t_update_for[$q_wo_so_table][$t_set_at] = $value[0];
							break;
						case preg_match("/ASSY_NO/", $key, $match):
							$t_set_at = "number='%s'";
							$t_update_for[$q_assembly_table][$t_set_at] = $value[0];
							break;
						case preg_match("/REVISION/", $key, $match):
							$t_set_at = "revision='%s'";
							$t_update_for[$q_assembly_table][$t_set_at] = $value[0];
							break;
						case preg_match("/CUST_NAME/", $key, $match):
							$t_set_at = "name='%s'";
							$t_update_for[$q_customer_table][$t_set_at] = $value[0];
							break;
						case preg_match("/CUST_PO_NO/", $key, $match):
							$t_set_at = "pono='%d'";
							$t_update_for[$q_customer_table][$t_set_at] = $value[0];
							break;
						default:
							# possibility to perform update all with those 3 queries OR use count(array per table)>0 as condition to update an entire table selectively.
							break;
					}
				}

				$result['update_prep'] = $t_update_for;
				// prepare update query

				// status<0 deactive, status = 0 obselete, status>0 active, status= 1: , status=2: , status=3: recently updated.
				// on implode add status = 3 by default for having updated received.

				/*if (empty($XT_diff) && empty($XT_null) && !empty($XT_same)) throw new Exception('06 - XT_Compare results in NO diff');
				if (!empty($XT_null)) throw new Exception('07 - XT_Compare results in NULL fields in MantisDb. Update?');
				if ( (!empty($XT_diff) && empty($XT_same) && empty($XT_null))
				   || (!empty($XT_diff) && $T_res_array['WO_NO'] === $X_res_array['WO_NO'] && empty($XT_null))) throw new Exception('04 - Ready to insert ALL mantis_db_invoke_insert()');*/ // meaning the entire Mantis entry is different  very unlikely to happen

				if (empty($o_Mocha)) throw new Exception(' 13 - Mocha isn\'t passed into this method for configuring tables');

				// Unfolding the defined mantis set of tables
 				$q_update_wo_so_table    	= $o_Mocha->update_wo_so_table;
				$q_update_assembly_table 	= $o_Mocha->update_assembly_table;
				$q_update_customer_table 	= $o_Mocha->update_customer_table;
				$q_query_sync_table 		= $o_Mocha->query_sync_table;

				$q_query_sync_table_find 	= $o_Mocha->query_sync_table_find;
				$q_query_sync_table_insert 	= $o_Mocha->query_sync_table_insert;


 				if (count($t_update_for[$q_wo_so_table])>0){
					$t_qr[$q_wo_so_table][$t_timestamp] = $this->update_a_record($t_update_for[$q_wo_so_table],$q_update_wo_so_table, $q_wo_so_table, $t_wo);
					$t_query[$q_wo_so_table] = $t_qr[$q_wo_so_table][$t_timestamp]['query_string'][0];
				}
				if (count($t_update_for[$q_assembly_table])>0){
					$t_qr[$q_assembly_table][$t_timestamp] = $this->update_a_record($t_update_for[$q_assembly_table],$q_update_assembly_table, $q_assembly_table, $t_uniq_key);
					$t_query[$q_assembly_table] = $t_qr[$q_assembly_table][$t_timestamp]['query_string'][0];
				}
				if (count($t_update_for[$q_customer_table])>0){
					$t_qr[$q_customer_table][$t_timestamp] = $this->update_a_record($t_update_for[$q_customer_table],$q_update_customer_table, $q_customer_table, $t_customer_id);
					$t_query[$q_customer_table] = $t_qr[$q_customer_table][$t_timestamp]['query_string'][0];
				}

				$result['pending_stock'] = $t_qr;
				$t_query_text = HelperUTILS::input_string_escape(implode('; ', $t_query));
				$result['update_stock'] = $t_query;
				$t_remark = 'query.wo = ' . $this->getQueryTrigger() . '\t\t' . 'query.customer_name = ' .$X_res_array['CUST_NAME'] . '\t\t'. 'query.creator_id = '. $this->getCreatorId();

				$response = HelperUTILS::mantis_db_query($q_query_sync_table_find, $q_query_sync_table, $t_query_text);
				if ($response['response']['count']>0) {
					$result['response']['stock'] = $response['response'];
					throw new Exception('06 - query_text exists');
				}

				$result['stock'] = HelperUTILS::mantis_db_query_insert($q_query_sync_table_insert, $q_query_sync_table, $t_query_text, $t_remark, $this->getCreatorId(), $t_timestamp, 0, 0, 0);

			} else throw new Exception('01 - invalid response from Manex');

		} catch (Exception $e) {
			$result['response']['error'] = 'ERROR ' . $e->getMessage(). ' , Reporter: xt_sync_update';
		}
		finally	{
			return $result;
			// .sync.response = { response :'json', error: 'string if there is error', stock: 'obj'}
		}
	}
}