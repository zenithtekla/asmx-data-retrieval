<?php
# query trigger is the word the user enters on an input field in the view page
# Approach: query directly on the Manex server, should query result performed on Mantis DB returns none.
# It is possible to perform check if query result $count < 1 and $_POST["query_trigger"].length() match a specific number of chars to perform db_update
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


/*function loadCore(){
	@require_once 'core.php';
}

loadCore();
// require_once dirname(__FILE__). '\manextis_test_config.php';
// require_once dirname(__FILE__). '\tests\TestConfig.php';
require_mantis_core();
require_once 'core/utility_api.php';
*/

require_once __ROOT__.'core.php';
// the extra APIs can be left out to enhance processing time.
require_once __ROOT__. 'core\config_api.php';
require_once __ROOT__. 'core\current_user_api.php';?>

<pre>
<?php
/*echo getDateTime(). "\n\n"; // 86400 for daily update, 3600 for hourly updatek, 2764800 for monthly update*/

$t_unix_today = strtotime(getDateTime());
// echo date("m/d/Y h:i:s a", $t_unix_today). "\n\n";

// load query strings
$qrs = HelperUTILS::load_conf(__CFG_FILE__);
$t_mocha_test = $qrs["MOCHA_TEST"];

$t_query_trigger = ($t_mocha_test) ? "asiakas1" : ($_POST["query_trigger"] || "");

$t_query = HelperUTILS::input_string_escape($t_query_trigger);
// with . The following can be commented out.
$response = HelperUTILS::mantis_db_query($qrs["MANTIS_QUERY_CUSTOMER_FIND"], $t_query);

$result = [
	"Mocha" => $t_mocha_test,
	"queryExeTime" => $t_unix_today,
	"queryTrigger_1" => $t_query,
	"RESPONSE_1" => $response
];
// use json_encode($response["response"]); to prepare Typeahead selectives

$t_process = new SkewChess($t_unix_today);
$t_retrieval_data = ($qrs["MOCHA_TEST"]) ? "05/23/2016" : date("m/d/Y", $t_unix_today);
$args = [$qrs, $response["count"], $qrs["MANEX_HTTP_REQ_ACCT_DATE"], $t_retrieval_data];

$response = $t_process->fn_skew_manexDb($args); // once found 1 customer, skip further process.
$result["RESPONSE_11"] = $response;

/* ---
		END RESPONSE_1
		BEGIN RESPONSE_2
   ---
*/

$t_query_trigger = ($t_mocha_test) ? "00091519A" : ($_POST["query_trigger"] || "");

$t_query = HelperUTILS::input_string_escape($t_query_trigger);
$response = HelperUTILS::mantis_db_query($qrs["MANTIS_QUERY_WO_FIND"], $t_query);

$result["queryTrigger_2"] = $t_query;
$result["RESPONSE_2"] = $response;

$args = [$qrs, $response["count"], $qrs["MANEX_HTTP_REQ_SO_WO"], $_POST["query_trigger"]];
$response = $t_process->fn_skew_manexDb($args);
$result["RESPONSE_22"] = $response;
echo json_encode($result, JSON_PRETTY_PRINT);
?>
</pre>