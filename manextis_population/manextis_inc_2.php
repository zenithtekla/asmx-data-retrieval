<?php
# query trigger is the word the user enters on an input field in the view page
# Approach: query directly on the Manex server, should query result performed on Mantis DB returns none.
# It is possible to perform check if query result $count < 1 and $t_query_trigger.length() match a specific number of chars to perform db_update
# TODO: mantis_db_query with array $q and different query_string -> capability improvement

/**
	* @package DbSkewer
	* @copyright [Env-System] Copyright (C) 2002 - 2014  MantisBT Team - mantisbt-dev@lists.sourceforge.net
	* @copyright [OnTop-Dev] Copyright (C) 2016 ZeTek - https://github.com/zenithtekla
	*/
	/**
	* DbSkewer
*/

header('Content-Type: application/json');
define('__ROOT__', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('__CFG_FILE__', __ROOT__.'cfg\manextis_conf.ini');
require_once __ROOT__.'core\manextis_utils.php';
require_once __ROOT__.'core\date_time.php';
require_once __ROOT__.'core.php';

// load datetime
$t_unix_today = strtotime(getDateTime());
// load query strings
$qrs = HelperUTILS::load_conf(__CFG_FILE__);

/*
// for POST METHOD
$t_query_trigger = file_get_contents("php://input");
$t_query_trigger = json_decode($t_query_trigger, TRUE);
echo json_encode(array("mySearch" => $t_query_trigger), JSON_PRETTY_PRINT);*/

// GET METHOD

$t_mocha_test = $_GET["Mocha"] === 'true' || $qrs["MOCHA_TEST"] == true;
$t_query_trigger = ($t_mocha_test) ? "00091519A" : $_GET["query"];

// $result["Mocha"] = $qrs["MOCHA_TEST"];
$result["Mocha"] = $_GET["Mocha"];
$result["queryExeTime"] = $t_unix_today;

$t_query_trigger = HelperUTILS::input_string_escape($t_query_trigger);
$result["queryTrigger"] = $t_query_trigger;
$response = HelperUTILS::mantis_db_query($qrs["MANTIS_QUERY_SO_FIND"], $t_query_trigger);
/* To find the remaining fields;
if ($response["count"] > 0) {
    //do another HelperUTILS::mantis_db_query with join.SQL.query and return $response
	$response = HelperUTILS::mantis_db_query($qrs["EXTRA_QUERY"], args);
}
*/

$result = array_merge($result, $response);

$args = [$qrs, $response["count"], $qrs["MANEX_HTTP_REQ_SO_WO"], $t_query_trigger];

$t_process = new SkewChess();
$response = $t_process->fn_skew_manexDb($args);
if ($response)
	$result["response"] = $response;

echo json_encode($result, JSON_PRETTY_PRINT);
?>