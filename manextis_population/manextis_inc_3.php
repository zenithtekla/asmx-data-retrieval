<?php
define('__ROOT__', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('__CFG_FILE__', __ROOT__.'cfg\manextis_conf.ini');
require_once __ROOT__.'core\manextis_utils.php';
require_once __ROOT__.'core\date_time.php';
require_once __ROOT__.'core.php';

// echo 'Current PHP version: ' . phpversion();
// load query strings
$qrs = HelperUTILS::load_conf(__CFG_FILE__);

$t_query_trigger = '1893';
$t_query_params = ['so0002', 8];
$t_query_param1 = 'so0002';
$t_query_param2 = 8;

$t_query_trigger = HelperUTILS::input_string_escape($t_query_trigger);
$result["queryTrigger"] = $t_query_trigger;


$response = HelperUTILS::mantis_db_query($qrs["MANTIS_QUERY_WO_FIND"], $t_query_trigger);
$result = array_merge($result, ["res1" => $response]);

/* ---
		END RESPONSE_1
		BEGIN RESPONSE_2
   ---
*/

$response = HelperUTILS::mantis_db_query($qrs["MANTIS_QUERY_SO_FIND"], $t_query_param1, $t_query_param2);
$result = array_merge($result, ["res2" => $response]);

/* ---
		END RESPONSE_2
		BEGIN RESPONSE_3
   ---
*/

$response = HelperUTILS::mantis_db_query($qrs["MANTIS_QUERY_EXECUTE_UPDATE"], $t_query_trigger);
$result = array_merge($result, ["res3" => $response]);

/* ---
		END RESPONSE_3
		BEGIN RESPONSE_4
   ---
*/

$response = HelperUTILS::mantis_db_query_insert('INSERT', $qrs["MANTIS_QUERY_INSERT_WO_TABLE"], 'wono4', 'sono4', 44, 444444, "_X444444");
$result = array_merge($result, ["res4" => $response]);

/* ---
		END RESPONSE_4
		BEGIN RESPONSE_5
   ---
*/

$response = HelperUTILS::mantis_db_query_insert($qrs["MANTIS_QUERY_INSERT_WO_TABLE"], [
	['wono3', 'sono3', 33, 333333, "_X333333"],
	['wono2', 'sono2', 22, 222222, "_X222222"]
]);
$result = array_merge($result, ["res5" => $response]);
?>
<pre>
<?php echo json_encode($result, JSON_PRETTY_PRINT); ?>
</pre>