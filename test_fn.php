<?php
header('Content-Type: application/json');
require_once("core/util_fn.php");
require_once("core/date_time.php");
// TODO: include db_conn if needed

echo getDateTime(). "\n\n"; // 86400 for daily update, 3600 for hourly updatek, 2764800 for monthly update

$t_unix_today = strtotime(getDateTime());
echo $t_unix_today. "\n\n";
echo date("m/d/Y h:i:s a", $t_unix_today). "\n\n";
echo date("m/d/Y", $t_unix_today). "\n\n";


/*{
$counter = 0;
if ($t_unix_today - $max_timestamp > 3600 && $automatic_updated) || ($t_unix_today - $max_timestamp > 60 && $button_clicked) {
	update exec
	$counter++;
} else
}*/

/*$t_db_update
existing prune 30 days old issue
event_hook*/

$t_process = new dbUTILS();
$t_process->fn_database_update_customer($t_unix_today);
/*$t_temp = $t_process->fn_db_update_customer;
$t_temp();*/
$t_process->setRetrievalDate("04/04/2016");
$t_retrieve_date= $t_process->getRetrievalDate();
echo $t_retrieve_date;
$t_getCurlData = $t_process->getCurlData();
echo $t_getCurlData;

?>
