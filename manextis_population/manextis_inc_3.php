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

$t_query_trigger = HelperUTILS::query_trigger_handler($t_query_trigger);
$result['queryTrigger'] = $t_query_trigger;

$response = HelperUTILS::mantis_db_query($qrs['MANTIS']['QUERY_WO_FIND'], $qrs['MANTIS']['wo_so_table'], $t_query_trigger);
$result = array_merge($result, ['res1' => $response]);

/* ---
		END Response_1 SELECT based on WO
		BEGIN Response_2 SELECT based on SO w/ parameter array
   ---
*/

$response = HelperUTILS::mantis_db_query($qrs['MANTIS']['QUERY_SO_FIND'], ['mantis_plugin_serials_serial_table', $t_query_param1, $t_query_param2]);
$result = array_merge($result, ['res2' => $response]);

/* ---
		END Response_2 SELECT based on SO - param array
		BEGIN Response_3 SELECT based on SO - params straight
   ---
*/

$response = HelperUTILS::mantis_db_query($qrs['MANTIS']['QUERY_SO_FIND'], 'mantis_plugin_serials_serial_table', $t_query_param1, $t_query_param2);
$result = array_merge($result, ['res3' => $response]);
/* ---
		END Response_3
		BEGIN Response_4
   ---
*/
$t_query_param1 = 'John Doe';
$t_query_param2 = 'John.pono.001';
$t_query_param3 = 2;
$t_query_param4 = 1461875091;
$t_query_param5 = 1461876888;

$response = HelperUTILS::mantis_db_query(
	$qrs['MANTIS']['QUERY_INSERT_CUSTOMER_TABLE'],

	[$qrs['MANTIS']['customer_table'], $t_query_param1, $t_query_param2, $t_query_param3, $t_query_param4, $t_query_param5],
	[$qrs['MANTIS']['customer_table'], 'Jane Doe', 'Jane.pono.001', 3, 1461809999, 1461900002],
	[$qrs['MANTIS']['customer_table'], 'Jane Doe', 'Jane.pono.001', 3, 1461809999, 1461900002]
);
$result = array_merge($result, ['res4' => $response]);

/* ---
		END Response_4
		BEGIN Response_5
   ---
*/

$response = HelperUTILS::mantis_db_query($qrs['MANTIS']['QUERY_INSERT_CUSTOMER_TABLE'], $qrs['MANTIS']['customer_table'], 'Jean Doe', 'jean.pono.002', 3, 1461801111, 1461702001);
$result = array_merge($result, ['res5' => $response]);
?>
<pre>
<?php print_r($result); ?>
</pre>